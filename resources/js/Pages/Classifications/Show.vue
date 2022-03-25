<template>
    <app-layout :title="title">

        <page-toolbar-nav
            title="Roles"
            :links="navLinks"
        />
        <gym-revenue-crud
            base-route="classifications"
            model-name="Classification"
            model-key="classification"
            :fields="fields"
            :resource="classifications"
            :actions="{
                trash: {
                    handler: ({ data }) => handleClickTrash(data),
                },
            }"
        />
        <confirm
            title="Really Trash Security Role?"
            v-if="confirmTrash"
            @confirm="handleConfirmTrash"
            @cancel="confirmTrash = null"
        >
            Are you sure you want to move Classification '{{
                confirmTrash.role
            }}' to the trash?<BR />
        </confirm>
    </app-layout>
</template>
<script>
import { defineComponent, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm";

import Button from "@/Components/Button";
import JetBarContainer from "@/Components/JetBarContainer";
import PageToolbarNav from "@/Components/PageToolbarNav";

export default defineComponent({
    components: {
        AppLayout,
        GymRevenueCrud,
        Confirm,
        JetBarContainer,
        Button,
        PageToolbarNav
    },
    props: ["classifications", "filters"],
    setup(props) {
        console.log({classifications: props.classifications})

        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };

        const handleConfirmTrash = () => {
            //Inertia.delete(route("classification.trash", confirmTrash.value));
            confirmTrash.value = null;
        };

        const fields = ["title", "created_at", "updated_at"];

        let navLinks = [
            {
                label: "Users",
                href: route("users"),
                onClick: null,
                active: false
            },
            {
                label: "Roles",
                href: route("roles"),
                onClick: null,
                active: false
            },
            {
                label: "Classification",
                href: route("classifications"),
                onClick: null,
                active: true
            }
        ];

        return {fields, confirmTrash, handleConfirmTrash, handleClickTrash, Inertia, navLinks};
    },
});
</script>
