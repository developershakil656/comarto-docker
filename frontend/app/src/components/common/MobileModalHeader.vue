<template>
<div class="md:hidden sticky top-0 z-[60] bg-white border-b border-gray-200">
	<div class="h-14 flex items-center px-3">
		<button @click="$emit('back')" class="mr-2 p-2 -ml-2 rounded-full hover:bg-gray-100 active:bg-gray-200">
			<svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
			</svg>
		</button>
		<h2 class="text-base font-semibold text-gray-900 truncate">{{ title }}</h2>
	</div>
</div>
</template>

<script>
export default {
	name: 'MobileModalHeader',
	props: {
		title: {
			type: String,
			default: ''
		}
	},
	emits: ['back']
}
</script>
