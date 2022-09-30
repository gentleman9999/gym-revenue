<template>
    <LayoutHeader title="Dynamic Reports" />
    <page-toolbar-nav title="Dynamic Reports" :links="navLinks" />
    <gym-revenue-crud
        base-route="dynamic-reports"
        model-name="DynamicReport"
        model-key="reports"
        :fields="fields"
        :resource="reports"
        :actions="{
            trash: {
                handler: ({ data }) => handleClickTrash(data),
            },
            run: {
                label: 'Run',
                handler: ({ data }) => handleClickRun(data),
            },
        }"
    />
    <confirm
        title="Really Trash Report?"
        v-if="confirmTrash"
        @confirm="handleConfirmTrash"
        @cancel="confirmTrash = null"
    >
        Are you sure you want to move Reports '{{ confirmTrash.title }}' to the
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

export default defineComponent({
    components: {
        LayoutHeader,
        GymRevenueCrud,
        Confirm,
        JetBarContainer,
        Button,
        PageToolbarNav,
    },
    props: ["reports", "filters"],
    setup(props) {
        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };

        const handleConfirmTrash = () => {
            Inertia.delete(route("reports", confirmTrash.value.id));
            confirmTrash.value = null;
        };

        const fields = ["name", "created_at", "updated_at"];

        let navLinks = [
            {
                label: "Dynamic Reports",
                href: route("dynamic-reports.show"),
                onClick: null,
                active: false,
            },
        ];

        const handleClickRun = (id) => {
            window.location.href = id.filters;
        };

        return {
            fields,
            confirmTrash,
            handleConfirmTrash,
            handleClickRun,
            handleClickTrash,
            Inertia,
            navLinks,
        };
    },
});
</script>
