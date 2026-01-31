<template>
  <div>
    <div class="h-full flex flex-col bg-blue-50">
      <!-- <div class="hidden md:block">
			<TopHeader />
		</div> -->
      <div
        class="flex lg:text-sm text-xs bg-white/80 backdrop-blur-sm h-[calc(100vh-10px)] md:h-[calc(100vh-84px)]"
      >
        <!-- Sidebar (hidden on mobile) -->
        <aside
          class="hidden lg:flex border-r border-gray-200/50 flex-col bg-white/90 backdrop-blur-sm min-w-[27%] max-w-[27%]"
        >
          <div class="p-4 flex-shrink-0">
            <div class="relative">
              <input
                v-model="threadSearch"
                type="text"
                placeholder="Search conversations..."
                class="w-full border-0 rounded-2xl px-4 py-3 pl-12 text-sm bg-gray-100/80 focus:bg-white focus:ring-2 focus:ring-primary/20 focus:shadow-lg outline-none transition-all duration-300"
              />
              <svg
                class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                ></path>
              </svg>
            </div>
          </div>

          <!-- Filters -->
          <div class="px-4 pb-3 flex-shrink-0">
            <div class="flex bg-gray-100/60 rounded-2xl p-1.5 backdrop-blur-sm">
              <button @click="setFilter('all')" :class="buttonClass('all')">
                All
              </button>
              <button
                @click="setFilter('buyers')"
                :class="buttonClass('buyers')"
              >
                Buyers
                <span
                  v-if="buyerUnreadCount > 0"
                  class="absolute -top-1 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full shadow-lg"
                  >{{ buyerUnreadCount }}</span
                >
              </button>
              <button
                @click="setFilter('sellers')"
                :class="buttonClass('sellers')"
              >
                Sellers
                <span
                  v-if="sellerUnreadCount > 0"
                  class="absolute -top-1 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full shadow-lg"
                  >{{ sellerUnreadCount }}</span
                >
              </button>
            </div>
          </div>

          <div
            class="px-4 pb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider flex-shrink-0"
          >
            Conversations
          </div>
          <div class="flex-1 overflow-y-auto px-2">
            <button
              v-for="c in filteredConversations"
              :key="c.id"
              @click="openThread(c)"
              class="w-full text-left px-3 py-3 hover:bg-gray-50/80 rounded-2xl flex gap-3 items-start transition-all duration-200 group"
              :class="{
                'bg-gradient-to-r from-primary/20 to-primary/10 border-l-4 border-primary shadow-md ring-2 ring-primary/20':
                  routeId == c.id,
                'hover:shadow-sm': routeId != c.id,
              }"
            >
              <div class="relative">
                <img
                  :src="getAvatar(c)"
                  class="w-12 h-12 rounded-full border-2 border-white shadow-lg object-cover"
                />
                <div
                  class="absolute -bottom-1 -right-1 w-4 h-4 border-2 border-white rounded-full"
                  :class="c.is_online ? 'bg-green-500' : 'bg-gray-300'"
                  :title="c.is_online ? 'Online' : 'Offline'"
                ></div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                  <div
                    class="font-bold text-gray-900 truncate group-hover:text-primary transition-colors duration-200"
                  >
                    {{ partnerName(c) }}
                  </div>
                  <div class="flex items-center gap-2">
                    <span
                      v-if="c.unread_count > 0"
                      class="bg-red-500 text-white text-xs px-2 py-1 rounded-full shadow-lg font-bold"
                      >{{ c.unread_count }}</span
                    >
                    <div class="text-[11px] text-gray-500 font-medium">
                      {{ diffForHumans(c.last_message?.created_at) }}
                    </div>
                  </div>
                </div>
                <div class="text-sm text-gray-600 truncate font-medium">
                  {{ c.last_message?.message || "Start the conversation" }}
                </div>
              </div>
            </button>
          </div>
        </aside>

        <!-- Messages area -->
        <section
          class="flex flex-col w-full md:w-[60%] bg-white/95 backdrop-blur-sm shadow-lg md:m-3 rounded-md"
        >
          <!-- Header -->
          <div
            v-if="currentConversation"
            class="h-16 border-b rounded-t-md shadow-sm border-gray-200/50 flex items-center justify-between px-4 md:px-6 bg-white/80 backdrop-blur-sm"
          >
            <div class="flex items-center gap-2 lg:gap-4">
              <button
                @click="goBack"
                class="lg:hidden p-2 -ml-4 rounded-full hover:bg-gray-100"
              >
                <ArrowLeftIcon class="w-5 text-gray-600" />
              </button>
              <img
                :src="getAvatar(currentConversation)"
                class="w-12 h-12 rounded-full object-cover"
              />
              <div>
                <div class="font-bold text-lg text-gray-900 line-clamp-1">
                  {{ partnerName(currentConversation) }}
                </div>
                <div class="text-sm text-gray-500 font-medium">
                  {{ getConversationType(currentConversation) }} conversation
                </div>
              </div>
            </div>
            <!-- Mobile 3-dot to open details modal -->
            <button
              @click="toggleDetailsModal"
              class="md:hidden p-2 rounded-full hover:bg-gray-100"
            >
              <InformationCircleIcon class="w-6 text-gray-600" />
            </button>
          </div>

          <!-- Messages -->
          <div
            ref="scrollArea"
            class="flex-1 overflow-y-auto px-6 py-4 space-y-4 bg-gray-50"
          >
            <div v-if="currentConversation">
              <div>
                <div
                  v-for="m in messages"
                  :key="m.id"
                  class="flex animate-fade-in"
                  :class="
                    m.sender_type === currentConversation?.my_role
                      ? 'justify-end'
                      : 'justify-start'
                  "
                >
                  <div
                    :class="[
                      'max-w-[70%] px-4 py-3 rounded-2xl shadow-lg mb-1 message-bubble',
                      m.sender_type === currentConversation?.my_role
                        ? 'bg-primary text-white rounded-br-md'
                        : 'bg-white rounded-bl-md shadow-gray-200/50',
                    ]"
                  >
                    <div
                      class="text-sm break-words"
                      v-html="formatMessage(m.message)"
                    ></div>

                    <!-- Error indicator and resend button -->
                    <div v-if="m.error" class="mt-2 flex items-center gap-2">
                      <div class="flex items-center gap-1 text-red-500 text-xs">
                        <svg
                          class="w-4 h-4"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                          ></path>
                        </svg>
                        Failed to send
                      </div>
                      <button
                        @click="resendMessage(m)"
                        class="text-xs bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded transition-colors duration-200"
                      >
                        Resend
                      </button>
                    </div>

                    <!-- Attachments -->
                    <div
                      v-if="getMessageAttachments(m).length > 0"
                      class="mt-2 space-y-2"
                    >
                      <div
                        v-for="attachment in getMessageAttachments(m)"
                        :key="attachment.id || attachment.name"
                        class="overflow-hidden rounded-lg border"
                        :class="
                          m.sender_type === currentConversation?.my_role
                            ? 'border-white/20'
                            : 'border-gray-200'
                        "
                      >
                        <!-- Image attachment -->
                        <div
                          v-if="isAttachmentImage(attachment)"
                          class="attachment-preview no-copy"
                          @click="
                            openImageGallery(
                              getMessageAttachments(m),
                              attachment
                            )
                          "
                        >
                          <img
                            :src="getAttachmentUrl(attachment)"
                            class="max-h-48 w-full object-cover cursor-pointer"
                            @contextmenu.prevent
                            @dragstart.prevent
                            @selectstart.prevent
                            @error="handleImageError"
                            alt="Attachment"
                          />
                        </div>
                        <!-- Document attachment -->
                        <div
                          v-else
                          class="flex items-center gap-3 px-3 py-2 text-sm"
                          :class="
                            m.sender_type === currentConversation?.my_role
                              ? 'bg-white/10'
                              : 'bg-gray-50 hover:bg-gray-100'
                          "
                        >
                          <PaperClipIcon class="w-5 h-5 text-gray-500" />
                          <div class="flex-1 min-w-0">
                            <div class="font-medium truncate">
                              {{
                                attachment.original_name ||
                                attachment.name ||
                                "Document"
                              }}
                            </div>
                            <div class="text-xs opacity-70">
                              {{ formatFileSize(attachment.size) }}
                            </div>
                          </div>
                          <button
                            @click="downloadAttachment(attachment)"
                            class="text-white hover:text-gray-200 transition-colors"
                            title="Download file"
                          >
                            <svg
                              class="w-4 h-4"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                              ></path>
                            </svg>
                          </button>
                        </div>
                      </div>
                    </div>

                    <div
                      v-if="linkPreviews(m.message).length"
                      class="mt-2 space-y-2"
                    >
                      <div
                        v-for="p in linkPreviews(m.message)"
                        :key="p.url"
                        class="link-preview-container overflow-hidden rounded-lg border"
                        :class="
                          m.sender_type === currentConversation?.my_role
                            ? 'border-white/20'
                            : 'border-gray-200'
                        "
                      >
                        <!-- Image preview -->
                        <img
                          v-if="p.type === 'image'"
                          :src="p.url"
                          class="link-preview-image max-h-48 w-full object-cover"
                        />
                        <!-- YouTube thumbnail -->
                        <div
                          v-else-if="p.type === 'youtube'"
                          class="relative group cursor-pointer"
                          @click="openYouTubeVideo(p.url)"
                        >
                          <img
                            :src="`https://img.youtube.com/vi/${p.videoId}/maxresdefault.jpg`"
                            :alt="`YouTube video: ${p.hostname}`"
                            class="w-full h-32 object-cover rounded-lg"
                            @error="handleYouTubeThumbnailError"
                          />
                          <!-- Play button overlay -->
                          <div
                            class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center rounded-lg group-hover:bg-opacity-40 transition-all duration-200"
                          >
                            <div
                              class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center shadow-lg group-hover:bg-red-700 transition-colors duration-200"
                            >
                              <svg
                                class="w-8 h-8 text-white"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                              >
                                <path d="M8 5v14l11-7z" />
                              </svg>
                            </div>
                          </div>
                          <!-- Video info -->
                          <div
                            class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3 rounded-b-lg"
                          >
                            <div class="text-white text-sm font-medium">
                              {{ p.hostname }}
                            </div>
                            <div class="text-white/80 text-xs">
                              Click to watch on YouTube
                            </div>
                          </div>
                        </div>
                        <!-- Enhanced link card -->
                        <a
                          v-else
                          :href="p.url"
                          target="_blank"
                          rel="nofollow noopener noreferrer"
                          class="block p-3 hover:bg-opacity-80 transition-all duration-200"
                          :class="
                            m.sender_type === currentConversation?.my_role
                              ? 'bg-white/10'
                              : 'bg-gray-50 hover:bg-gray-100'
                          "
                        >
                          <div class="flex items-start gap-3">
                            <!-- Favicon -->
                            <div
                              v-if="shouldShowFavicon(p.hostname)"
                              class="flex-shrink-0"
                            >
                              <img
                                :src="`https://www.google.com/s2/favicons?domain=${p.hostname}&sz=32`"
                                class="w-6 h-6 rounded"
                                :alt="`${p.hostname} favicon`"
                                @error="$event.target.style.display = 'none'"
                              />
                            </div>
                            <!-- Link info -->
                            <div class="flex-1 min-w-0">
                              <div
                                class="font-semibold text-sm text-gray-900 truncate"
                              >
                                {{ p.hostname }}
                              </div>
                              <div class="text-xs text-gray-900 truncate mt-1">
                                {{ p.url }}
                              </div>
                              <div
                                class="text-xs text-blue-400 mt-2 font-medium"
                              >
                                Click to visit →
                              </div>
                            </div>
                          </div>
                        </a>
                      </div>
                    </div>
                    <div
                      class="text-[10px] opacity-70 mt-2 text-right font-medium flex items-center justify-end gap-1"
                    >
                      <span>{{ diffForHumans(m.created_at) }}</span>
                      <span
                        v-if="m.sender_type === currentConversation?.my_role"
                      >
                        <svg
                          v-if="m.is_read"
                          xmlns="http://www.w3.org/2000/svg"
                          class="inline w-4 h-4"
                          :class="
                            m.sender_type === currentConversation?.my_role
                              ? 'text-white'
                              : 'text-gray-500'
                          "
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        >
                          <path d="M1 14l5 5L20 5" />
                          <path d="M9 14l3 3L23 6" />
                        </svg>
                        <svg
                          v-else
                          xmlns="http://www.w3.org/2000/svg"
                          class="inline w-4 h-4"
                          :class="
                            m.sender_type === currentConversation?.my_role
                              ? 'text-white/50'
                              : 'text-gray-500'
                          "
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        >
                          <path d="M2 14l5 5L22 4" />
                        </svg>
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Typing indicator -->
                <div
                  v-if="isTyping"
                  class="flex justify-start animate-slide-in"
                >
                  <div
                    class="bg-white rounded-2xl rounded-bl-md shadow-lg px-4 py-3 typing-indicator"
                  >
                    <div class="flex items-center gap-1">
                      <div class="typing-dot"></div>
                      <div class="typing-dot"></div>
                      <div class="typing-dot"></div>
                    </div>
                    <span class="text-xs text-gray-500 ml-2 font-medium"
                      >typing...</span
                    >
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="h-full flex items-center justify-center">
              <div class="text-center animate-scale-in">
                <div
                  class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg"
                >
                  <svg
                    class="w-12 h-12 text-primary"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                    ></path>
                  </svg>
                </div>
                <div class="text-gray-800 font-bold text-2xl mb-2">
                  Welcome to Messages
                </div>
                <div class="text-gray-500 font-medium text-lg mb-3">
                  Start connecting with businesses and customers
                </div>
                <div class="text-gray-400 text-sm">
                  Choose a conversation from the sidebar to begin messaging
                </div>
                <div class="mt-6 flex justify-center space-x-4">
                  <div class="flex items-center text-xs text-gray-400">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                    Online
                  </div>
                  <div class="flex items-center text-xs text-gray-400">
                    <svg
                      class="w-4 h-4 mr-2"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                      ></path>
                    </svg>
                    Secure
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Input -->
          <div
            v-if="currentConversation"
            class="border-t rounded-b-md border-gray-200/50 bg-white/95 backdrop-blur-sm flex-shrink-0"
          >
            <!-- File preview area -->
            <div
              v-if="selectedFiles.length > 0"
              class="px-4 py-3 border-b border-gray-200/30 bg-gray-50"
            >
              <div class="flex flex-wrap gap-3">
                <div
                  v-for="(file, index) in selectedFiles"
                  :key="index"
                  class="relative group"
                >
                  <!-- Image preview -->
                  <div v-if="isImageFile(file)" class="relative">
                    <img
                      :src="getFilePreview(file)"
                      class="w-20 h-20 object-cover rounded-xl border-2 border-white shadow-lg"
                    />
                    <button
                      @click="removeFile(index)"
                      class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow-lg"
                    >
                      <XMarkIcon class="w-3 h-3" />
                    </button>
                  </div>
                  <!-- Document preview -->
                  <div v-else class="relative">
                    <div
                      class="w-20 h-20 bg-gray-100 rounded-xl border-2 border-white shadow-lg flex items-center justify-center"
                    >
                      <PaperClipIcon class="w-8 h-8 text-gray-500" />
                    </div>
                    <button
                      @click="removeFile(index)"
                      class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow-lg"
                    >
                      <XMarkIcon class="w-3 h-3" />
                    </button>
                    <div
                      class="text-xs text-gray-600 mt-2 text-center truncate w-20 font-medium"
                    >
                      {{ file.name }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Message input -->
            <div class="mx-1 lg:px-4 py-3 flex items-center gap-1 lg:gap-3">
              <!-- Hidden file input -->
              <input
                ref="fileInputRef"
                type="file"
                multiple
                accept="image/*,.pdf,.doc,.docx,.xlsx"
                class="hidden"
                @change="handleFileSelect"
              />

              <!-- Attachment button -->
              <button
                @click="triggerFileSelect"
                class="text-gray-500 hover:text-primary transition-colors duration-200 p-1 rounded-full hover:bg-gray-100/80"
              >
                <PaperClipIcon class="w-6 h-6" />
              </button>

              <!-- Image button -->
              <button
                @click="triggerImageSelect"
                class="text-gray-500 hover:text-primary transition-colors duration-200 p-1 rounded-full hover:bg-gray-100/80"
              >
                <PhotoIcon class="w-6 h-6" />
              </button>

              <div class="w-full relative">
                <textarea
                  ref="messageTextarea"
                  v-model="newMessage"
                  placeholder="Type message..."
                  rows="1"
                  class="flex-1 bg-gray-100/80 rounded-2xl px-5 py-3.5 text-sm outline-none focus:bg-white focus:ring-2 focus:ring-primary/20 focus:shadow-lg transition-all duration-300 resize-none w-full"
                  @keydown="handleKeyDown"
                  @input="autoResize"
                />
                <button
                  class="bg-primary text-white p-2 rounded-2xl text-sm font-semibold flex items-center gap-2 shadow-lg hover:shadow-xl transition-all duration-200 absolute right-1 bottom-3"
                  :disabled="!newMessage.trim() && selectedFiles.length === 0"
                  @click="sendMessage"
                >
                  <!-- <span class="hidden lg:block">Send</span> -->
                  <PaperAirplaneIcon class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>
        </section>

        <!-- Desktop details sidebar -->
        <aside
          v-if="currentConversation"
          class="hidden min-w-[23%] flex-col md:flex border-l border-gray-200/50 flex-col bg-white/90 backdrop-blur-sm m-3 rounded-md"
        >
          <div class="p-6 overflow-y-auto">
            <div class="text-center">
              <div class="relative mx-auto mb-4 inline-block">
                <img
                  :src="getAvatar(currentConversation)"
                  class="w-24 h-24 rounded-full border-4 border-white shadow-xl object-cover mx-auto"
                />
              </div>
              <div class="flex items-center justify-center gap-2">
                <div
                  class="text-center font-bold text-xl text-gray-900 mb-1 capitalize"
                >
                  {{ partnerName(currentConversation) }}
                </div>
              </div>
              <div
                class="text-center text-sm text-gray-500 font-medium mb-4 flex justify-center items-center gap-1"
              >
                {{ getConversationType(currentConversation) }}
                <VerifiedBadge
                  :accountVerified="currentConversation.account_verification"
                />
              </div>

              <!-- View Business Details Button (for sellers) -->
              <div
                v-if="currentConversation.my_role === 'user'"
                class="mb-6 text-center"
              >
                <router-link
                  :to="`/${currentConversation.profile.slug}`"
                  class="inline-flex items-center px-3 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors shadow-md"
                >
                  <svg
                    class="w-4 h-4 mr-2"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"
                    ></path>
                  </svg>
                  View Business Details
                </router-link>
              </div>

              <!-- Rating -->
              <div
                v-if="currentConversation.profile?.rate"
                class="mb-6 text-center"
              >
                <div class="flex items-center justify-center gap-3">
                  <div
                    class="flex bg-primary font-bold px-3 py-2 rounded-xl items-center shadow-lg"
                  >
                    <span class="text-white">{{
                      currentConversation.profile.rate?.rate || "0.0"
                    }}</span>
                    <StarIcon class="text-yellow-400 lg:h-4 h-3 pl-1" />
                  </div>
                  <span class="text-gray-600 font-medium"
                    >{{
                      currentConversation.profile.rate.count || 0
                    }}
                    Ratings</span
                  >
                </div>
              </div>

              <!-- Address -->
              <div
                v-if="
                  currentConversation.profile?.full_address &&
                  currentConversation.profile.full_address.trim() !== ','
                "
                class="mb-6"
              >
                <div
                  class="text-xs text-gray-500 uppercase mb-2 font-semibold tracking-wider"
                >
                  Address
                </div>
                <div
                  class="text-sm text-gray-700 bg-gray-50/80 rounded-xl p-3 font-medium"
                >
                  {{ formatAddress(currentConversation.profile.full_address) }}
                </div>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>

    <!-- Mobile details slide-over drawer -->
    <div
      v-if="showDetailsModal && currentConversation"
      class="lg:hidden fixed inset-0 z-[60]"
    >
      <div
        class="absolute inset-0 bg-black/40"
        @click="toggleDetailsModal"
      ></div>
      <div
        class="absolute inset-y-0 right-0 w-11/12 max-w-sm bg-white shadow-xl p-6 overflow-y-auto"
        :class="animationClass"
        @click.stop
      >
        <div class="flex items-center justify-between mb-4">
          <div class="font-bold text-lg">Conversation Details</div>
          <button
            class="p-2 rounded-full hover:bg-gray-100"
            @click="toggleDetailsModal"
            aria-label="Close"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
        <div class="text-center">
          <div class="relative mx-auto mb-4 inline-block">
            <img
              :src="getAvatar(currentConversation)"
              class="w-24 h-24 rounded-full border-4 border-white shadow-xl object-cover mx-auto"
            />
          </div>
          <div class="flex items-center justify-center gap-2">
            <div
              class="text-center font-bold text-xl text-gray-900 mb-1 capitalize"
            >
              {{ partnerName(currentConversation) }}
            </div>
          </div>
          <div
            class="text-center text-sm text-gray-500 font-medium mb-4 flex justify-center items-center gap-1"
          >
            {{ getConversationType(currentConversation) }}
            <VerifiedBadge
              :accountVerified="currentConversation.account_verification"
            />
          </div>
          <div
            v-if="currentConversation.profile?.rate"
            class="mb-6 text-center"
          >
            <div class="flex items-center justify-center gap-3">
              <div
                class="flex bg-primary font-bold px-3 py-2 rounded-xl items-center shadow-lg"
              >
                <span class="text-white text-xl">{{
                  currentConversation.profile.rate?.rate || "0.0"
                }}</span>
                <StarIcon class="text-yellow-400 h-6 pl-1" />
              </div>
              <span class="text-sm text-gray-600 font-medium"
                >{{ currentConversation.profile.rate.count || 0 }} Ratings</span
              >
            </div>
          </div>
          <div
            v-if="currentConversation.my_role === 'user'"
            class="mb-6 text-center"
          >
            <router-link
              :to="`/${currentConversation.profile.slug}`"
              class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors shadow-md"
            >
              <svg
                class="w-4 h-4 mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"
                ></path>
              </svg>
              View Business Details
            </router-link>
          </div>
          <div
            v-if="
              currentConversation.profile?.full_address &&
              currentConversation.profile.full_address.trim() !== ','
            "
            class="mb-6 text-left"
          >
            <div
              class="text-xs text-gray-500 uppercase mb-2 font-semibold tracking-wider"
            >
              Address
            </div>
            <div
              class="text-sm text-gray-700 bg-gray-50/80 rounded-xl p-3 font-medium"
            >
              {{ formatAddress(currentConversation.profile.full_address) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Attachment Preview Modal -->
    <AttachmentPreviewModal
      :attachment="previewModal.attachment"
      :is-visible="previewModal.isVisible"
      :all-images="previewModal.allImages"
      :current-index="previewModal.currentIndex"
      @close="closePreviewModal"
      @next-image="nextImage"
      @previous-image="previousImage"
      @go-to-image="goToImage"
    />
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import VerifiedBadge from "@/components/common/VerifiedBadge.vue";
import AttachmentPreviewModal from "@/components/common/AttachmentPreviewModal.vue";
import OptimizedImage from '@/components/common/OptimizedImage.vue';
import {
  PhotoIcon,
  PaperClipIcon,
  XMarkIcon,
  PaperAirplaneIcon,
  ArrowLeftIcon,
  InformationCircleIcon,
} from "@heroicons/vue/24/outline";
import { StarIcon } from "@heroicons/vue/24/solid";
import axios from "axios";

export default {
  name: "ConversationMessagesView",
  components: {
    VerifiedBadge,
    AttachmentPreviewModal,
    OptimizedImage,
    PhotoIcon,
    PaperClipIcon,
    XMarkIcon,
    StarIcon,
    PaperAirplaneIcon,
    ArrowLeftIcon,
    InformationCircleIcon,
  },
  data() {
    return {
      animationClass: "",
      newMessage: "",
      threadSearch: "",
      activeFilter: "all",
      showDetailsModal: false,
      selectedFiles: [],
      fileInputRef: null,
      isTyping: false,
      previewModal: {
        isVisible: false,
        attachment: null,
        allImages: [],
        currentIndex: 0,
      },
      activeConversationId: null,
      currentAbortController: null,
    };
  },
  computed: {
    ...mapGetters(["conversations", "messages"]),
    currentConversation() {
      return this.conversations.find((c) => c.id == this.routeId) || null;
    },
    routeId() {
      return this.$route.params.id;
    },
    filteredConversations() {
      const q = this.threadSearch.trim().toLowerCase();
      let conversations = this.conversations || [];

      // Apply filter
      if (this.activeFilter === "buyers") {
        conversations = conversations.filter((c) => c.my_role === "business");
      } else if (this.activeFilter === "sellers") {
        conversations = conversations.filter((c) => c.my_role === "user");
      }

      // Apply search
      if (!q) return conversations;
      return conversations.filter(
        (c) =>
          this.partnerName(c).toLowerCase().includes(q) ||
          (c.last_message?.message || "").toLowerCase().includes(q)
      );
    },
    buyerUnreadCount() {
      return (this.conversations || [])
        .filter((c) => c.my_role === "business" && c.unread_count > 0)
        .reduce((sum, c) => sum + c.unread_count, 0);
    },
    sellerUnreadCount() {
      return (this.conversations || [])
        .filter((c) => c.my_role === "customer" && c.unread_count > 0)
        .reduce((sum, c) => sum + c.unread_count, 0);
    },
  },
  methods: {
    checkScreenSize() {
      // Tailwind's 'md' breakpoint is 768px
      this.isDesktop = window.innerWidth >= 768;
      this.$emit("show-header", this.isDesktop);
    },
    toggleDetailsModal() {
      if (this.showDetailsModal) {
        this.animationClass = "animate-slide-out-right";
        setTimeout(() => {
          this.showDetailsModal = false;
        }, 500); // Match animation duration
      } else {
        this.animationClass = "animate-slide-in-right";
        this.showDetailsModal = true;
      }
    },
    partnerName(c) {
      return c?.profile?.name || "Conversation";
    },
    getAvatar(c) {
      return c?.profile?.profile || "https://placehold.co/150x150/0b845c/white?text=Profile";
    },
    getConversationType(c) {
      return c?.my_role === "business" ? "Buyer" : "Seller";
    },
    formatAddress(address) {
      if (!address) return "";
      const parts = address
        .split(",")
        .map((part) => part.trim())
        .filter((part) => part && part !== ",");
      return parts.join(", ");
    },
    formatMessage(text) {
      return (text || "").replace(/\n/g, "<br/>");
    },
    diffForHumans(input) {
      const d = new Date(input);
      if (isNaN(d)) return "";
      const now = new Date();
      const sec = Math.round(Math.abs(d - now) / 1000);
      const min = Math.round(sec / 60);
      const hr = Math.round(min / 60);
      const day = Math.round(hr / 24);
      if (sec < 45) return `${sec} seconds ago`;
      if (min < 45) return `${min} minutes ago`;
      if (hr < 36) return `${hr} hours ago`;
      if (day < 26) return `${day} days ago`;
      const month = Math.round(day / 30);
      if (month < 11) return `${month} months ago`;
      const year = Math.round(month / 12);
      return `${year} years ago`;
    },
    setFilter(filter) {
      this.activeFilter = filter;
    },
    buttonClass(filter) {
      return [
        "relative px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 flex-1 text-center",
        this.activeFilter === filter
          ? "bg-white text-primary shadow-sm"
          : "text-gray-600 hover:text-gray-900 hover:bg-white/50",
      ];
    },
    handleKeyDown(event) {
      if (event.key === "Enter" && !event.shiftKey) {
        event.preventDefault();
        this.sendMessage();
      }
    },
    goBack() {
      this.$router.go(-1);
    },
    autoResize(event) {
      // Use the event target if it exists, otherwise use the ref
      const textarea = event?.target || this.$refs.messageTextarea;

      if (!textarea) return;

      textarea.style.height = "auto";
      textarea.style.height = Math.min(textarea.scrollHeight, 120) + "px";
    },
    async openThread(c) {
      if (Number(this.routeId) === c.id) return;
      await this.$router.push({
        name: "conversation-messages",
        params: { id: c.id },
      });
    },
    async loadThread(conversationId) {
  // 1. Abort any previous pending request
  if (this.currentAbortController) {
    this.currentAbortController.abort();
  }

  // 2. Create a new controller for this specific request
  this.currentAbortController = new AbortController();
  this.activeConversationId = conversationId;

  try {
    // 3. Clear the UI immediately so the user doesn't see old data
    this.$store.commit("SET_MESSAGES", []);

    // 4. Dispatch with the signal
    await this.$store.dispatch("fetchConversationMessages", {
      conversationId,
      signal: this.currentAbortController.signal
    });

    // 5. Verify we are still on the same conversation before showing UI changes
    if (this.activeConversationId !== conversationId) return;

    // Handle Drafts
    const draftKey = `conversation_draft_${conversationId}`;
    const draftMessage = localStorage.getItem(draftKey);
    if (draftMessage) {
      this.newMessage = draftMessage;
      localStorage.removeItem(draftKey);
      this.$nextTick(() => this.autoResize());
    }

    this.$nextTick(() => {
      setTimeout(() => this.scrollToBottom(), 100);
    });
  } catch (error) {
    // Silence abort errors, handle others
    if (error.name !== 'AbortError') {
      console.error("Failed to load thread:", error);
    }
  }
},
    scrollToBottom() {
      const el = this.$refs.scrollArea;
      if (!el) return;
      requestAnimationFrame(() =>
        setTimeout(() => {
          el.scrollTop = el.scrollHeight;
        }, 80)
      );
    },
    async sendMessage() {
      if (
        (!this.newMessage.trim() && this.selectedFiles.length === 0) ||
        !this.currentConversation
      )
        return;

      const text = this.newMessage.trim();
      const files = [...this.selectedFiles];

      this.newMessage = "";
      this.selectedFiles = [];

      this.$nextTick(() => {
        const textarea = this.$refs.messageTextarea;
        if (textarea) {
          textarea.style.height = "auto";
        }
      });

      const optimisticMessage = {
        id: "temp-" + Date.now(),
        message: text,
        sender_type: this.currentConversation?.my_role,
        created_at: new Date().toLocaleTimeString(),
        attachments: files.map((file) => ({
          id: "temp-" + Date.now() + Math.random(),
          url: this.isImageFile(file) ? URL.createObjectURL(file) : null,
          original_name: file.name,
          mime_type: file.type,
          size: file.size,
        })),
      };

      this.$store.commit("ADD_OPTIMISTIC_MESSAGE", optimisticMessage);
      this.$nextTick(() => this.scrollToBottom());

      try {
        await this.$store.dispatch("sendConversationMessage", {
          conversationId: this.currentConversation.id,
          message: text,
          attachments: files,
        });

        this.$store.commit("REMOVE_OPTIMISTIC_MESSAGE", optimisticMessage.id);

        setTimeout(async () => {
          try {
            await this.$store.dispatch("fetchConversations");
          } catch (error) {
            console.error("Error refreshing conversations:", error);
          }
        }, 300);

        this.$nextTick(() => this.scrollToBottom());
      } catch (error) {
        console.error("Error sending message:", error);
        this.$store.commit("REMOVE_OPTIMISTIC_MESSAGE", optimisticMessage.id);
        alert("Failed to send message. Please try again.");
      }
    },
    escapeHtml(str) {
      if (typeof str !== "string") return "";
      return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    },
    autolink(str) {
      if (!str) return "";
      const urlRegex = /((https?:\/\/|www\.)[^\s<]+)/gi;
      return str.replace(urlRegex, (match) => {
        const hasProtocol = /^https?:\/\//i.test(match);
        const url = hasProtocol ? match : `http://${match}`;
        return `<a href="${url}" target="_blank" rel="nofollow noopener noreferrer" class="underline text-blue-400">${match}</a>`;
      });
    },
    formatMessage(text) {
      const safe = this.escapeHtml(text || "");
      const linked = this.autolink(safe);
      return linked.replace(/\n/g, "<br/>");
    },
    extractUrls(text) {
      if (!text || typeof text !== "string") return [];
      const regex = /((https?:\/\/|www\.)[^\s<]+)/gi;
      const urls = [];
      let m;
      while ((m = regex.exec(text)) !== null) {
        const raw = m[0];
        const hasProtocol = /^https?:\/\//i.test(raw);
        const url = hasProtocol ? raw : `http://${raw}`;
        urls.push(url);
      }
      return Array.from(new Set(urls));
    },
    extractImageUrls(text) {
      if (!text || typeof text !== "string") return [];
      const imageRegex =
        /(https?:\/\/[^\s<]+\.(jpg|jpeg|png|gif|webp|bmp|svg)(\?[^\s<]*)?)/gi;
      const urls = [];
      let m;
      while ((m = imageRegex.exec(text)) !== null) {
        urls.push(m[0]);
      }
      return Array.from(new Set(urls));
    },
    parseYoutubeId(url) {
      try {
        const u = new URL(url);
        if (u.hostname.includes("youtu.be")) {
          return u.pathname.slice(1) || null;
        }
        if (u.hostname.includes("youtube.com")) {
          if (u.pathname.startsWith("/watch")) return u.searchParams.get("v");
          if (u.pathname.startsWith("/shorts/"))
            return u.pathname.split("/")[2] || null;
        }
        return null;
      } catch (e) {
        return null;
      }
    },
    isImageUrl(url) {
      return /(\.png|\.jpg|\.jpeg|\.gif|\.webp)(\?.*)?$/i.test(url);
    },
    getHostname(url) {
      try {
        return new URL(url).hostname.replace(/^www\./, "");
      } catch (e) {
        return url;
      }
    },
    shouldShowFavicon(hostname) {
      const invalidHosts = [
        "localhost",
        "127.0.0.1",
        "0.0.0.0",
        "::1",
        "192.168.",
        "10.",
        "172.",
      ];
      return !invalidHosts.some((invalid) => hostname.includes(invalid));
    },
    linkPreviews(text) {
      if (!text || typeof text !== "string") return [];
      const urls = this.extractUrls(text);
      const previews = urls
        .map((u) => {
          try {
            const videoId = this.parseYoutubeId(u);
            if (videoId) {
              return {
                type: "youtube",
                url: u,
                videoId,
                hostname: this.getHostname(u),
              };
            }
            if (this.isImageUrl(u)) {
              return { type: "image", url: u, hostname: this.getHostname(u) };
            }
            return { type: "link", url: u, hostname: this.getHostname(u) };
          } catch (error) {
            return null;
          }
        })
        .filter(Boolean);
      return previews;
    },
    openYouTubeVideo(url) {
      window.open(url, "_blank", "noopener,noreferrer");
    },
    handleYouTubeThumbnailError(event) {
      const videoId = event.target.src.match(/\/vi\/([^\/]+)\//)?.[1];
      if (videoId) {
        event.target.src = `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
      }
    },
    triggerFileSelect() {
      this.$refs.fileInputRef.click();
    },
    triggerImageSelect() {
      const input = document.createElement("input");
      input.type = "file";
      input.multiple = true;
      input.accept = "image/*";
      input.onchange = (e) => this.handleFileSelect(e);
      input.click();
    },
    handleFileSelect(event) {
      const files = Array.from(event.target.files);
      const validFiles = files.filter((file) => {
        if (file.size > 5 * 1024 * 1024) {
          alert(`File ${file.name} is too large. Maximum size is 5MB.`);
          return false;
        }

        const allowedTypes = [
          "image/jpeg",
          "image/png",
          "image/jpg",
          "image/webp",
          "application/pdf",
          "application/msword",
          "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
          "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        ];
        if (!allowedTypes.includes(file.type)) {
          alert(`File type ${file.type} is not supported.`);
          return false;
        }

        return true;
      });

      this.selectedFiles.push(...validFiles);
      event.target.value = "";
    },
    removeFile(index) {
      const file = this.selectedFiles[index];
      if (file && file.previewUrl) {
        URL.revokeObjectURL(file.previewUrl);
      }
      this.selectedFiles.splice(index, 1);
    },
    isImageFile(file) {
      return file.type.startsWith("image/");
    },
    getFilePreview(file) {
      if (this.isImageFile(file)) {
        if (!file.previewUrl) {
          file.previewUrl = URL.createObjectURL(file);
        }
        return file.previewUrl;
      }
      return null;
    },
    isAttachmentImage(attachment) {
      const mimeType =
        attachment.mime_type || attachment.mimeType || attachment.type;
      const isImage = mimeType && mimeType.startsWith("image/");

      const fileName =
        attachment.original_name ||
        attachment.name ||
        attachment.filename ||
        "";
      const hasImageExtension = /\.(jpg|jpeg|png|gif|webp|bmp|svg)$/i.test(
        fileName
      );

      const url =
        attachment.url ||
        attachment.file_url ||
        attachment.download_url ||
        attachment.path ||
        "";
      const urlIsImage = this.isImageUrl(url);

      return isImage || hasImageExtension || urlIsImage;
    },
    formatFileSize(bytes) {
      if (!bytes || bytes === 0) return "Unknown size";
      const k = 1024;
      const sizes = ["Bytes", "KB", "MB", "GB"];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
    },
    downloadAttachment(attachment) {
      const link = document.createElement("a");
      link.href = this.getAttachmentUrl(attachment);
      link.download =
        attachment.original_name ||
        attachment.name ||
        attachment.filename ||
        "download";
      link.target = "_blank";
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    },
    getAttachmentUrl(attachment) {
      return (
        attachment.url ||
        attachment.file_url ||
        attachment.download_url ||
        attachment.path ||
        ""
      );
    },
    handleImageError(event) {
      console.error("Image failed to load:", event.target.src);
    },
    getFileNameFromUrl(url) {
      try {
        const urlObj = new URL(url);
        const pathname = urlObj.pathname;
        const filename = pathname.split("/").pop();
        return filename || "attachment";
      } catch (e) {
        return "attachment";
      }
    },
    getMimeTypeFromUrl(url) {
      try {
        const filename = this.getFileNameFromUrl(url);
        const extension = filename.split(".").pop()?.toLowerCase();

        const mimeTypes = {
          jpg: "image/jpeg",
          jpeg: "image/jpeg",
          png: "image/png",
          gif: "image/gif",
          webp: "image/webp",
          bmp: "image/bmp",
          svg: "image/svg+xml",
          pdf: "application/pdf",
          doc: "application/msword",
          docx: "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
          xlsx: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
          xls: "application/vnd.ms-excel",
          txt: "text/plain",
        };

        return mimeTypes[extension] || "application/octet-stream";
      } catch (e) {
        return "application/octet-stream";
      }
    },
    getMessageAttachments(message) {
      if (message.attachments && Array.isArray(message.attachments)) {
        const result = message.attachments.map((attachment, index) => {
          if (typeof attachment === "string") {
            return {
              id: index,
              url: attachment,
              original_name: this.getFileNameFromUrl(attachment),
              mime_type: this.getMimeTypeFromUrl(attachment),
              size: 0,
            };
          }
          return attachment;
        });
        return result;
      }

      if (message.attachments && typeof message.attachments === "string") {
        try {
          const parsed = JSON.parse(message.attachments);
          if (Array.isArray(parsed)) {
            return parsed.map((attachment, index) => {
              if (typeof attachment === "string") {
                return {
                  id: index,
                  url: attachment,
                  original_name: this.getFileNameFromUrl(attachment),
                  mime_type: this.getMimeTypeFromUrl(attachment),
                  size: 0,
                };
              }
              return attachment;
            });
          }
          return [];
        } catch (e) {
          console.error("Failed to parse attachments JSON:", e);
          return [];
        }
      }

      if (message.files && Array.isArray(message.files)) {
        return message.files;
      }

      if (message.media && Array.isArray(message.media)) {
        return message.media;
      }

      if (message.message && typeof message.message === "string") {
        const imageUrls = this.extractImageUrls(message.message);
        if (imageUrls.length > 0) {
          return imageUrls.map((url, index) => ({
            id: `text-${index}`,
            url: url,
            original_name: this.getFileNameFromUrl(url),
            mime_type: this.getMimeTypeFromUrl(url),
            size: 0,
          }));
        }
      }

      return [];
    },
    openImageGallery(images, currentImage) {
      const allImages = this.getAllConversationImages();

      if (allImages.length === 0) {
        allImages.push(currentImage);
      }

      this.previewModal.attachment = currentImage;
      this.previewModal.isVisible = true;
      this.previewModal.allImages = allImages;

      const normalizedCurrent = this.normalizeImageObject(currentImage);
      const normalizedAllImages = allImages.map((img) =>
        this.normalizeImageObject(img)
      );
      let foundIndex = normalizedAllImages.findIndex(
        (img) => img.normalized_key === normalizedCurrent.normalized_key
      );

      if (foundIndex === -1 && normalizedCurrent.id) {
        foundIndex = normalizedAllImages.findIndex(
          (img) => img.id === normalizedCurrent.id
        );
      }

      if (foundIndex === -1 && normalizedCurrent.url) {
        foundIndex = normalizedAllImages.findIndex(
          (img) => img.url === normalizedCurrent.url
        );
      }

      this.previewModal.currentIndex = foundIndex === -1 ? 0 : foundIndex;
    },
    normalizeImageObject(img) {
      return {
        id: img.id || img.attachment_id || img.file_id || null,
        url:
          img.url ||
          img.file_url ||
          img.download_url ||
          img.path ||
          img.src ||
          "",
        name: img.name || img.original_name || img.filename || img.title || "",
        size: img.size || img.file_size || 0,
        mime_type:
          img.mime_type || img.mimeType || img.type || img.content_type || "",
        normalized_key: `${
          img.url || img.file_url || img.download_url || img.path || ""
        }_${img.name || img.original_name || img.filename || ""}_${
          img.size || 0
        }`,
      };
    },
    getAllConversationImages() {
      const allImages = [];

      this.messages.forEach((message, index) => {
        const messageAttachments = this.getMessageAttachments(message);

        messageAttachments.forEach((attachment) => {
          if (this.isAttachmentImage(attachment)) {
            allImages.push(attachment);
          }
        });
      });

      return allImages;
    },
    nextImage() {
      if (this.previewModal.allImages.length > 1) {
        this.previewModal.currentIndex =
          (this.previewModal.currentIndex + 1) %
          this.previewModal.allImages.length;
        this.previewModal.attachment =
          this.previewModal.allImages[this.previewModal.currentIndex];
      }
    },
    previousImage() {
      if (this.previewModal.allImages.length > 1) {
        this.previewModal.currentIndex =
          this.previewModal.currentIndex === 0
            ? this.previewModal.allImages.length - 1
            : this.previewModal.currentIndex - 1;
        this.previewModal.attachment =
          this.previewModal.allImages[this.previewModal.currentIndex];
      }
    },
    goToImage(index) {
      if (
        this.previewModal.allImages.length > 0 &&
        index >= 0 &&
        index < this.previewModal.allImages.length
      ) {
        this.previewModal.currentIndex = index;
        this.previewModal.attachment = this.previewModal.allImages[index];
      }
    },
    closePreviewModal() {
      this.previewModal.isVisible = false;
      this.previewModal.attachment = null;
      this.previewModal.allImages = [];
      this.previewModal.currentIndex = 0;
    },
    clearUnread(id) {
        // Mark as read locally to update the global header count immediately
        this.$store.commit('UPDATE_CONVERSATION_UNREAD', {
            conversationId: parseInt(id),
            count: 0
        });
    }
  },
  async mounted() {
    console.log('mounted')
    if (!this.$store.getters.isAuthenticated) {
      this.$router.push({ name: "home" });
      return;
    }
    const token = this.$store.getters.token;
    if (token)
      axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    if (!this.conversations || this.conversations.length === 0) {
      await this.$store.dispatch("fetchConversations");
    }
    if (this.routeId) await this.loadThread(this.routeId);
    //hide footer
    this.checkScreenSize();
    this.$emit("show-footer", false);
    this.$emit("show-mobile-navigation", false);
  },
  watch: {
    routeId: {
    immediate: true,
    handler(newId, oldId) {
      if (!newId || newId === oldId) return;

      this.newMessage = '';
      
      // Reset height after the DOM clears newMessage
      this.$nextTick(() => {
        this.autoResize();
      });

      this.loadThread(newId);

      this.$store.commit("UPDATE_CONVERSATION_UNREAD", {
        conversationId: newId,
        count: 0,
      });
    }
  },
    // Watch for real-time updates in the conversations list
    conversations: {
        deep: true,
        handler(newList) {
            if (!this.routeId) return;

            // Find the current conversation in the updated list
            const current = newList.find(c => c.id == this.routeId);

            // If the current conversation now has unread messages, fetch them
            if (current && current.unread_count > 0) {
                console.log("New message detected for current thread, fetching...");

                this.loadThread(this.routeId);
                this.clearUnread(this.routeId);
            }
        }
    }
  },
  // Add this navigation guard
  beforeRouteLeave(to, from, next) {
    // When leaving the UserProfile section, tell the parent to show the header again
    this.$emit("show-header", true);
    this.$emit("show-footer", true);
    this.$emit("show-mobile-navigation", true);
    next();
  },
};
</script>

<style scoped>
/* Prevent text selection and copying */
.no-select {
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Prevent image dragging */
.no-drag {
  -webkit-user-drag: none;
  -khtml-user-drag: none;
  -moz-user-drag: none;
  -o-user-drag: none;
  user-drag: none;
}
@keyframes slide-in-right {
  0% {
    transform: translateX(100%);
  }
  100% {
    transform: translateX(0);
  }
}

@keyframes slide-out-right {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(100%);
  }
}

.animate-slide-in-right {
  animation: slide-in-right 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.animate-slide-out-right {
  animation: slide-out-right 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
/* Attachment preview styles */
.attachment-preview {
  position: relative;
  overflow: hidden;
  border-radius: 12px;
}

.attachment-preview img {
  transition: transform 0.2s ease;
}

.attachment-preview:hover img {
  transform: scale(1.02);
}

/* Custom animations */
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slide-in {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes scale-in {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

.animate-slide-in {
  animation: slide-in 0.3s ease-out;
}

.animate-scale-in {
  animation: scale-in 0.3s ease-out;
}

.slide-in-right {
  animation: slideInRight 0.2s ease-out;
}

/* Message bubble animations */
.message-bubble {
  transition: all 0.2s ease-out;
}

.message-bubble:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Typing indicator */
.typing-indicator {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 8px 12px;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.typing-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #9ca3af;
  animation: typing-bounce 1.4s infinite ease-in-out;
}

.typing-dot:nth-child(1) {
  animation-delay: -0.32s;
}
.typing-dot:nth-child(2) {
  animation-delay: -0.16s;
}

@keyframes typing-bounce {
  0%,
  80%,
  100% {
    transform: scale(0.9);
    opacity: 0.6;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}

/* Link preview styles */
.link-preview-container {
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  overflow: hidden;
  margin-top: 8px;
}

.link-preview-image {
  max-height: 192px !important;
  width: 100% !important;
  object-fit: cover !important;
  display: block !important;
  border-radius: 8px !important;
  min-height: 120px;
  height: auto !important;
  aspect-ratio: auto !important;
}

/* Ensure images don't cause layout shifts */
.attachment-preview img {
  max-height: 192px;
  width: 100%;
  object-fit: cover;
  display: block;
  border-radius: 8px;
}

/* Message bubble images */
.message-bubble img {
  max-height: 200px;
  width: 100%;
  object-fit: cover;
  display: block;
  border-radius: 8px;
}

/* Textarea styling */
textarea {
  font-family: inherit;
  line-height: 1.5;
  overflow-y: auto;
}

textarea:focus {
  outline: none;
}
</style>
