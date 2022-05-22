<template>
    <app-layout>
        <template #header> Attrition </template>

        <jet-bar-container> </jet-bar-container>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import JetBarContainer from "@/Components/JetBarContainer";

export default defineComponent({
    components: {
        AppLayout,
        JetBarContainer,
    },
});
</script>
