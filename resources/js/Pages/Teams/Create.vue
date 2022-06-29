<template>
    <ModalableWrapper>
        <LayoutHeader>
            <h2 class="font-semibold text-xl leading-tight">Create Team</h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <team-form />
        </template>
    </ModalableWrapper>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader";
import TeamForm from "@/Pages/Teams/Partials/TeamForm";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default defineComponent({
    components: {
        LayoutHeader,
        TeamForm,
        ModalableWrapper,
        ModalSlot,
    },
});
</script>
