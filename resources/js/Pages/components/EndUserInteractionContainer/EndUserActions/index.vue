<template>
    <div class="mr-4">
        <contact-button
            @click="handleClick('email')"
            method="email"
            :count="count"
        />
    </div>
    <div class="mr-4">
        <contact-button
            @click="handleClick('phone')"
            method="call"
            :count="count"
        />
    </div>
    <div class="mr-4">
        <contact-button
            @click="handleClick('sms')"
            method="sms"
            :count="count"
        />
    </div>
</template>
<script setup>
import ContactButton from "./ContactButton.vue";
const props = defineProps({
    count: {
        type: Number,
    },
    handleClick: {
        type: Function,
    },
});
</script>
