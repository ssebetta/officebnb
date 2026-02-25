<template>
    <div class="flex items-center gap-3">
        <img 
            :src="dark ? '/web/icon-512.png' : '/web/icon-512.png'" 
            alt="OfficeBnB Logo" 
            class="h-10 w-10 rounded-lg"
        />
        <div class="leading-tight">
            <p class="text-sm font-semibold tracking-tight" :class="dark ? 'text-gray-900' : 'text-white'">OfficeBnB</p>
            <p class="text-[0.7rem]" :class="dark ? 'text-gray-500' : 'text-slate-300'">Premium office stays on demand</p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    dark: {
        type: Boolean,
        default: false,
    },
});
</script>
