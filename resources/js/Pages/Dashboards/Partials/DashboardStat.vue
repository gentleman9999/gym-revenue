<template>
    <jet-bar-stat-card
        :title="title"
        :number="value"
        :type="type"
    >
        <template v-slot:icon>
            <jet-bar-icon :type="icon" fill />
        </template>
    </jet-bar-stat-card>
</template>

<script>
import { defineComponent } from "vue";
import JetBarStatCard from "@/Components/JetBarStatCard";
import JetBarIcon from "@/Components/JetBarIcon";

export default defineComponent({
    components:{
        JetBarStatCard,
        JetBarIcon,
    },
    props: {
        title: {
            type: String,
            required: true,
        },
        value: {
            type: Number,
            required: true,
        },
        type: {
            type: String,
            required: true,
        },
        icon: {
            type: String,
            required: true,
        }
    },
});
</script>
