<template>
    <LayoutHeader title="Departments" />
    <page-toolbar-nav title="Departments" :links="navLinks" />
    <ApolloQuery :query="(gql) => department_query" :variables="param">
        <template v-slot="{ result: { data } }">
            <gym-revenue-crud
                v-if="data"
                base-route="departments"
                model-name="Department"
                model-key="departments"
                :fields="fields"
                :resource="getDepartments(data)"
                @update-page="(value) => (param = { ...param, page: value })"
                :actions="{
                    trash: {
                        handler: ({ data }) => handleClickTrash(data),
                    },
                }"
            />
        </template>
    </ApolloQuery>
    <confirm
        title="Really Trash Department?"
        v-if="confirmTrash"
        @confirm="handleConfirmTrash"
        @cancel="confirmTrash = null"
    >
        Are you sure you want to move Departments '{{ confirmTrash.title }}' to
        the trash?<BR />
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
import gql from "graphql-tag";

export default defineComponent({
    components: {
        LayoutHeader,
        GymRevenueCrud,
        Confirm,
        JetBarContainer,
        Button,
        PageToolbarNav,
    },
    props: ["filters"],
    setup(props) {
        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };

        const handleConfirmTrash = () => {
            Inertia.delete(route("departments.trash", confirmTrash.value.id));
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
                active: true,
            },
            {
                label: "Positions",
                href: route("positions"),
                onClick: null,
                active: false,
            },
        ];
        const param = ref({
            page: 1,
        });
        const department_query = gql`
            query Departments($page: Int) {
                departments(page: $page) {
                    data {
                        id
                        name
                        created_at
                        updated_at
                    }
                    pagination: paginatorInfo {
                        current_page: currentPage
                        last_page: lastPage
                        from: firstItem
                        to: lastItem
                        per_page: perPage
                        total
                    }
                }
            }
        `;

        const getDepartments = (data) => {
            return _.cloneDeep(data.departments);
        };

        return {
            fields,
            confirmTrash,
            handleConfirmTrash,
            handleClickTrash,
            Inertia,
            navLinks,
            param,
            department_query,
            getDepartments,
        };
    },
});
</script>
