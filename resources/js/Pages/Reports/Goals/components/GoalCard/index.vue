<template>
    <div class="goal-card-wrapper">
        <div class="goal-card-header">
            <h5>Goal {{ data.id }}</h5>
            <div class="flex flex-row space-x-3 items-center">
                <chat-with-label :label="data.unread" />
                <sms-icon />
            </div>
        </div>
        <div class="goal-card-title">{{ data.title }}</div>
        <div class="goal-card-content">{{ data.content }}</div>
        <div class="goal-card-priority">
            <div
                :class="{
                    'text-error': data.priority === 'high',
                    'text-warning': data.priority === 'medium',
                    'text-accent': data.priority === 'low',
                }"
            >
                {{ data.priority }}
            </div>
            <div :class="{ 'text-accent': data.frequency === 'repeat' }">
                {{ data.frequency }}
            </div>
        </div>
        <div class="goal-card-actions">
            <select-box label="Goal Type" size="xs" :items="goal_types" />
            <Button secondary size="xs">Edit</Button>
        </div>
    </div>
</template>
<script setup>
import SmsIcon from "@/Components/Icons/SMS";
import ChatWithLabel from "@/Components/Icons/ChatWithLabel";
import SelectBox from "@/Components/SelectBox";
import Button from "@/Components/Button";

const props = defineProps({
    data: {
        type: Object,
        default: {},
    },
});

const goal_types = ["Company", "DMA", "Location", "Employee"];
</script>
<style scoped>
.goal-card-wrapper {
    @apply border border-error rounded p-4 bg-neutral-900;
}
.goal-card-header {
    @apply flex flex-row justify-between;
}
.goal-card-title {
    @apply text-xl text-secondary font-bold border-b border-neutral-500;
}
h5 {
    @apply text-lg font-bold;
}
.goal-card-content {
    @apply text-sm h-20 pt-2;
}
.goal-card-priority {
    @apply flex flex-row text-sm space-x-6 font-bold py-2 capitalize;
}
.goal-card-actions {
    @apply flex flex-row justify-between;
}
</style>
