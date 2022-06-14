<template>
    <ModalableWrapper>
        <app-layout title="Create Team">
            <template #header>
                <h2 class="font-semibold text-xl leading-tight">Create Team</h2>
            </template>

            <div>
                <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                    <ModalSlot />
                </div>
            </div>
        </app-layout>
        <template #modal>
            <team-form />
        </template>
    </ModalableWrapper>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import TeamForm from "@/Pages/Teams/Partials/TeamForm";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default defineComponent({
    components: {
        AppLayout,
        TeamForm,
        ModalableWrapper,
        ModalSlot,
    },
});
</script>
