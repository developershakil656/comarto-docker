<template>
    <div>
        <div class="flex items-center gap-2 cursor-pointer hover:bg-primary/15 duration-200 ease-in-out rounded-md py-3 px-2 my-1 select-none" @click="showHours = !showHours">
            <ClockIcon class="w-5 text-primary" />
            <span class="text-primary font-medium">{{ timeStatus }}</span>
            <ChevronDownIcon :class="{'rotate-180': showHours}" class="w-5 ml-auto transition-transform duration-300" />
        </div>
        <div v-if="showHours" class="my-2 px-2">
            <div v-for="(item, idx) in timings" :key="item.day" class="flex justify-between py-1" :class="{'font-semibold text-primary': idx === todayIdx}">
                <span>{{ item.day }}<span v-if="idx === todayIdx"> (Today)</span></span>
                <span>{{ item.time }}</span>
            </div>
        </div>
    </div>
</template>

<script>
import {
    ClockIcon,
    ChevronDownIcon
} from "@heroicons/vue/24/outline";

export default {
    components: {
        ClockIcon,
        ChevronDownIcon
    },
    props: {
        timing: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            showHours: false,
        }
    },
    computed: {
        timings() {
            if (!this.timing) return [];
            
            const days = ['sat', 'sun', 'mon', 'tue', 'wed', 'thu', 'fri'];
            const dayNames = ['SAT', 'SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI'];
            
            return days.map((day, index) => {
                const dayTiming = this.timing[day];
                let time = 'Closed';
                
                if (typeof dayTiming === 'string') {
                    time = dayTiming;
                } else if (dayTiming?.start && dayTiming?.end) {
                    time = `${dayTiming.start} - ${dayTiming.end}`;
                }
                
                return {
                    day: dayNames[index],
                    time: time
                };
            });
        },
        todayIdx() {
            // Remap JS days (0=Sun..6=Sat) to custom week starting on Saturday
            const idx = (new Date().getDay() + 1) % 7;
            // Ensure the index is within bounds
            return Math.min(Math.max(idx, 0), 6);
        },
        timeStatus() {
            const now = new Date();
            const today = this.timings[this.todayIdx];

            // Handle case when no timing data is available
            if (!today || !today.time) {
                return 'Timing not available';
            }

            // Handle 24 Hrs
            if (today.time.toLowerCase().includes('24 hrs')) {
                return 'Open 24 Hours';
            }

            // Handle Closed
            if (today.time.toLowerCase() === 'closed') {
                return 'Closed today';
            }

            const [openTime, closeTime] = today.time.split(' - ');
            const openDate = new Date(now);
            const closeDate = new Date(now);

            const parseTime = (timeStr, date) => {
                const [time, modifier] = timeStr.split(' ');
                let [hours, minutes] = time.split(':').map(Number);
                if (modifier.toLowerCase() === 'pm' && hours !== 12) hours += 12;
                if (modifier.toLowerCase() === 'am' && hours === 12) hours = 0;
                date.setHours(hours, minutes, 0, 0);
                return date;
            };

            parseTime(openTime, openDate);
            parseTime(closeTime, closeDate);

            if (now < openDate) {
                return `Opens at today ${openTime}`;
            } else if (now >= openDate && now < closeDate) {
                return `Open until ${closeTime}`;
            } else {
                // After closing, get next day's opening time
                const nextIdx = (this.todayIdx + 1) % 7;
                const nextDay = this.timings[nextIdx];
                if (!nextDay || !nextDay.time) {
                    return 'Timing not available';
                }
                if (nextDay.time.toLowerCase().includes('24 hrs')) {
                    return `Opens at ${nextDay.day} 12:00 am`;
                }
                const [nextOpenTime] = nextDay.time.split(' - ');
                return `Opens at ${nextDay.day} ${nextOpenTime}`;
            }
        }
    }
}
</script> 