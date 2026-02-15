<template>
<div>
  <router-view />
  <Notivue v-slot="item" class="!z-[99999]">
    <Notification :item="item" />
  </Notivue>
  <ToastNotification class="z-60"/>
</div>
</template>

<script setup>
import ToastNotification from './components/ToastNotification.vue'
import { Notivue, Notification } from 'notivue'
</script>