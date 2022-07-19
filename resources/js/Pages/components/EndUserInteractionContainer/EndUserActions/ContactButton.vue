<template>
    <Button
        type="button"
        v-bind="$attrs"
        :success="method == 'email'"
        :error="method == 'call'"
        :info="method == 'sms'"
    >
        {{ method }}
        <span
            class="bg-base-300 p-1 rounded text-xs ml-2"
            :class="{
                'text-success': method === 'email',
                'text-error': method === 'call',
                'text-info': method === 'sms',
            }"
        >
            {{ contactCount }}
        </span>
    </Button>
</template>
<script setup>
import { computed } from "vue";
import Button from "@/Components/Button.vue";

const props = defineProps({
    count: {
        type: Number,
        default: 0,
    },
    method: {
        type: String,
        default: "email",
    },
});

const contactCount = computed({
    get() {
        let ret = props.count.emailedCount;
        if (props.method == "call") {
            ret = props.count.calledCount;
        } else if (props.method == "sms") {
            ret = props.count.smsCount;
        }
        return ret;
    },
});
</script>
