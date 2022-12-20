<template>
    <LayoutHeader title="Positions" />
    <page-toolbar-nav title="Positions" :links="navLinks" />
    <gym-revenue-crud
        base-route="positions"
        model-name="Position"
        model-key="position"
        :fields="fields"
        :edit-component="PositionForm"
        :actions="{
            trash: {
                handler: ({ data }) => handleClickTrash(data),
            },
        }"
    />

    <confirm
        title="Really Trash Position?"
        v-if="confirmTrash"
        @confirm="handleConfirmTrash"
        @cancel="confirmTrash = null"
    >
        Are you sure you want to move Position '{{ confirmTrash.title }}' to the
        trash?<BR />
    </confirm>
</template>

<script>
import { defineComponent, ref } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm.vue";

import Button from "@/Components/Button.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import PageToolbarNav from "@/Components/PageToolbarNav.vue";
import PositionForm from "@/Pages/Positions/Partials/PositionForm.vue";

export default defineComponent({
    components: {
        LayoutHeader,
        GymRevenueCrud,
        Confirm,
        JetBarContainer,
        Button,
        PageToolbarNav,
        PositionForm,
    },
    props: ["filters"],
    setup(props) {
        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };

        const handleConfirmTrash = () => {
            Inertia.delete(route("positions.trash", confirmTrash.value.id));
            confirmTrash.value = null;
        };

        const fields = ["name", "created_at", "updated_at"];

        let navLinks = [
            {
                label: "Users",
                href: route("users"),
                onClick: null,
                active: false,
            },
            {
                label: "Security Roles",
                href: route("roles"),
                onClick: null,
                active: false,
            },
            {
                label: "Departments",
                href: route("departments"),
                onClick: null,
                active: false,
            },
            {
                label: "Positions",
                href: route("positions"),
                onClick: null,
                active: true,
            },
        ];
        return {
            fields,
            confirmTrash,
            handleConfirmTrash,
            handleClickTrash,
            Inertia,
            navLinks,
            PositionForm,
        };
    },
});
</script>
