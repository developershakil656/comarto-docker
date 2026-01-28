<template>
  <Transition name="modal" appear>
    <div v-if="open" class="fixed inset-0 z-[60] flex items-stretch md:items-center justify-center bg-white md:bg-black md:bg-opacity-30 p-0 md:p-4" style="min-height: 100vh;" @click="$emit('close')">
      <Transition name="modal-content" appear>
        <div class="bg-white w-full h-full md:h-auto md:rounded-xl md:shadow-lg md:max-w-2xl md:max-h-[90vh] overflow-y-auto" @click.stop>
          <MobileModalHeader title="Business Timings" @back="$emit('close')" />
          <div class="p-2 md:p-8 relative">
            <div class="bg-white rounded-2xl shadow-lg md:rounded-none md:shadow-none p-4 md:p-0">
            <button @click="$emit('close')" class="hidden md:block absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
              <XMarkIcon class="w-6 h-6" />
            </button>
            <h2 class="hidden md:block text-2xl font-bold mb-2">Business Timings</h2>
            <p class="text-gray-500 mb-6">Let your customers know when you are open for business</p>
            
            <form @submit.prevent="onSubmit" class="space-y-4">
              <div class="space-y-4">
                <div v-for="(day, idx) in daysOfWeek" :key="day" class="border rounded-lg p-4">
                  <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-bold">{{ day }}</label>
                    <div class="flex items-center gap-2">
                      <label class="flex items-center gap-2 text-sm">
                        <input 
                          type="checkbox" 
                          v-model="timeSlot.days[idx].is24Hours" 
                          @change="handle24HoursChange(idx)"
                        /> 24 Hours
                      </label>
                      <label class="flex items-center gap-2 text-sm">
                        <input 
                          type="checkbox" 
                          v-model="timeSlot.days[idx].isClosed" 
                          @change="handleClosedChange(idx)"
                        /> Closed
                      </label>
                    </div>
                  </div>
                  
                  <div v-if="!timeSlot.days[idx].is24Hours && !timeSlot.days[idx].isClosed" class="flex gap-4 items-end">
                    <div class="flex-1">
                      <label class="block text-sm font-medium">Open at</label>
                      <select 
                        v-model="timeSlot.days[idx].open" 
                        class="input" 
                        @change="updateTimingData"
                      >
                        <option value="">Select time</option>
                        <option v-for="time in timeOptions" :key="time" :value="time">{{ time }}</option>
                      </select>
                    </div>
                    <div class="flex-1">
                      <label class="block text-sm font-medium">Close at</label>
                      <select 
                        v-model="timeSlot.days[idx].close" 
                        class="input" 
                        @change="updateTimingData"
                      >
                        <option value="">Select time</option>
                        <option v-for="time in timeOptions" :key="time" :value="time">{{ time }}</option>
                      </select>
                    </div>
                  </div>
                  
                  <div v-else-if="timeSlot.days[idx].is24Hours" class="text-green-600 font-medium">
                    Open 24 Hours
                  </div>
                  
                  <div v-else-if="timeSlot.days[idx].isClosed" class="text-red-600 font-medium">
                    Closed
                  </div>
                </div>
              </div>
              
              <p v-if="timingWarning" class="text-red-500 text-sm ml-2">{{ timingWarning }}</p>
              
              <button 
                type="submit" 
                class="w-full mt-6 py-3 bg-primary text-white text-lg font-semibold rounded-lg shadow hover:bg-primary/85 transition disabled:opacity-60 disabled:cursor-not-allowed" 
                :disabled="!canContinueTimings() || loading"
              >
                <span v-if="loading" class="flex items-center justify-center">
                  <ArrowPathIcon class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" />
                  Saving...
                </span>
                <span v-else>
                  Save Timings
                </span>
              </button>
            </form>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script>
import { push } from 'notivue';
import {
    XMarkIcon,
    ArrowPathIcon
} from '@heroicons/vue/24/outline';
import MobileModalHeader from '@/components/common/MobileModalHeader.vue';
import { useModalScroll } from '@/composables/useModalScroll';

// Generate time options in AM/PM format
const generateTimeOptions = () => {
  const times = [];
  const ampm = ['am', 'pm'];
  for (let h = 0; h < 24; h++) {
    for (let m = 0; m < 60; m += 30) {
      let hour = h % 12 === 0 ? 12 : h % 12;
      let minute = m === 0 ? '00' : '30';
      let suffix = ampm[Math.floor(h / 12)];
      times.push(`${hour}:${minute} ${suffix}`);
    }
  }
  return times;
};

export default {
    name: 'BusinessTimingsModal',
    components: {
        XMarkIcon,
        ArrowPathIcon,
        MobileModalHeader
    },
    setup() {
        const { openModal, closeModal } = useModalScroll()
        return { openModal, closeModal }
    },
    props: {
        open: {
            type: Boolean,
            default: false
        },
        initialTimings: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        // Week starts with Saturday (matching RegisterBusinessView)
        const daysOfWeek = ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
        
        const timeSlot = {
            days: daysOfWeek.map(day => ({
                day: day,
                is24Hours: false,
                isClosed: false,
                open: '9:00 am',
                close: '10:00 pm',
            })),
        };

        return {
            daysOfWeek,
            timeSlot,
            timeOptions: generateTimeOptions(),
            timingWarning: '',
            timingData: {
                sat: null,
                sun: null,
                mon: null,
                tue: null,
                wed: null,
                thu: null,
                fri: null
            },
            loading: false
        };
    },
    watch: {
        'timeSlot.days': {
            handler() {
                this.updateTimingData();
            },
            deep: true,
            immediate: true
        },
        open(newVal) {
            if (newVal) {
                this.openModal('business-timings-modal');
                this.initializeTimings();
            } else {
                this.closeModal('business-timings-modal');
            }
        },
        initialTimings: {
            handler(newVal) {
                if (this.open) {
                    this.initializeTimings();
                }
            },
            deep: true
        }
    },
    methods: {
        handle24HoursChange(dayIndex) {
            const day = this.timeSlot.days[dayIndex];
            if (day.is24Hours) {
                day.isClosed = false;
                day.open = '';
                day.close = '';
            }
            this.updateTimingData();
        },
        
        handleClosedChange(dayIndex) {
            const day = this.timeSlot.days[dayIndex];
            if (day.isClosed) {
                day.is24Hours = false;
                day.open = '';
                day.close = '';
            }
            this.updateTimingData();
        },
        
        updateTimingData() {
            // Map days correctly: Sat=0, Sun=1, Mon=2, Tue=3, Wed=4, Thu=5, Fri=6
            const daysMapping = ['sat', 'sun', 'mon', 'tue', 'wed', 'thu', 'fri'];
            
            daysMapping.forEach((day, index) => {
                const daySettings = this.timeSlot.days[index];
                
                if (daySettings.is24Hours) {
                    this.timingData[day] = '24 Hrs';
                } else if (daySettings.isClosed) {
                    this.timingData[day] = 'Closed';
                } else if (daySettings.open && daySettings.close) {
                    this.timingData[day] = {
                        start: daySettings.open,
                        end: daySettings.close
                    };
                } else {
                    this.timingData[day] = null;
                }
            });
        },
        
        canContinueTimings() {
            return this.timeSlot.days.some(day => !day.isClosed);
        },
        
        initializeTimings() {
            const daysMapping = ['sat', 'sun', 'mon', 'tue', 'wed', 'thu', 'fri'];
            
            daysMapping.forEach((day, index) => {
                const dayData = this.initialTimings[day];
                const daySettings = this.timeSlot.days[index];
                
                if (dayData === '24 Hrs') {
                    daySettings.is24Hours = true;
                    daySettings.isClosed = false;
                    daySettings.open = '';
                    daySettings.close = '';
                } else if (dayData === 'Closed') {
                    daySettings.is24Hours = false;
                    daySettings.isClosed = true;
                    daySettings.open = '';
                    daySettings.close = '';
                } else if (dayData && typeof dayData === 'object') {
                    daySettings.is24Hours = false;
                    daySettings.isClosed = false;
                    daySettings.open = dayData.start;
                    daySettings.close = dayData.end;
                } else {
                    // Default values for unset days
                    daySettings.is24Hours = false;
                    daySettings.isClosed = false;
                    daySettings.open = '9:00 am';
                    daySettings.close = '10:00 pm';
                }
            });
        },
        
        async onSubmit() {
            if (!this.canContinueTimings()) {
                push.error('Please select at least one day to be open.');
                return;
            }
            this.timingWarning = '';
            this.loading = true;

            try {
                // Update business details with timing data
                await this.$store.dispatch('updateBusinessDetails', {
                    timing: this.timingData
                });
                
                // Close the modal
                this.$emit('close');
                push.success('Business timings updated successfully!');
            } catch (error) {
                push.error('Failed to update business timings. Please try again.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style scoped>
.input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  background: #f9fafb;
  font-size: 1rem;
  outline: none;
  transition: border 0.2s, background 0.3s, color 0.3s;
}
.input:focus {
  border-color: #2563eb;
  background: #fff;
}

/* Modal animations */
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-content-enter-active,
.modal-content-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-content-enter-from {
  opacity: 0;
  transform: scale(0.95) translateY(-20px);
}

.modal-content-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-20px);
}
</style>
