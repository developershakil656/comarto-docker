<template>
  <div class="flex flex-col min-h-screen w-full">
    <router-view/>
    <Notivue v-slot="item" class="!z-[99999]">
      <Notification :item="item" />
    </Notivue>
  </div>
</template>

<script setup>
import { Notivue, Notification } from 'notivue'
</script>

