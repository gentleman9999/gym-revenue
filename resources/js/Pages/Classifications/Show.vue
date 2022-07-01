<template>
    <LayoutHeader title="Classifications" />
    <page-toolbar-nav title="Classifications" :links="navLinks" />
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
        title="Really Trash Classification?"
        v-if="confirmTrash"
        @confirm="handleConfirmTrash"
        @cancel="confirmTrash = null"
    >
        Are you sure you want to move Classification '{{ confirmTrash.title }}'
        to the trash?<BR />
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

export default defineComponent({
    components: {
        LayoutHeader,
        GymRevenueCrud,
        Confirm,
        JetBarContainer,
        Button,
        PageToolbarNav,
    },
    props: ["classifications", "filters"],
    setup(props) {
        console.log({ classifications: props.classifications });

        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };

        const handleConfirmTrash = () => {
            Inertia.delete(
                route("classifications.trash", confirmTrash.value.id)
            );
            confirmTrash.value = null;
        };

        const fields = ["title", "created_at", "updated_at"];

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
                label: "Classification",
                href: route("classifications"),
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
        };
    },
});
</script>
