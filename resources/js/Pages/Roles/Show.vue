<template>
    <LayoutHeader title="Security Roles" />
    <page-toolbar-nav title="Security Roles" :links="navLinks" />
    <ApolloQuery :query="(gql) => role_query" :variables="param">
        <template v-slot="{ result: { data } }">
            <gym-revenue-crud
                v-if="data"
                base-route="roles"
                model-name="Role"
                model-key="role"
                :fields="fields"
                :resource="getRoles(data)"
                @update-page="(value) => (param = { ...param, page: value })"
                :actions="{
                    trash: false,
                    restore: false,
                    delete: {
                        label: 'Delete',
                        handler: ({ data }) => handleClickDelete(data),
                    },
                }"
            />
        </template>
    </ApolloQuery>
    <confirm
        title="Really Trash Security Role?"
        v-if="confirmDelete"
        @confirm="handleConfirmDelete"
        @cancel="confirmDelete = null"
    >
        Are you sure you want to delete Security Role '{{
            confirmDelete.title
        }}'
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
        const confirmDelete = ref(null);
        const handleClickDelete = (item) => {
            confirmDelete.value = item;
        };

        const handleConfirmDelete = () => {
            Inertia.delete(route("roles.delete", confirmDelete.value.id));
            confirmDelete.value = null;
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
                active: true,
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
                active: false,
            },
        ];

        const param = ref({
            page: 1,
        });
        const role_query = gql`
            query Roles($page: Int) {
                roles(page: $page) {
                    data {
                        id
                        title
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

        const getRoles = (data) => {
            return _.cloneDeep(data.roles);
        };
        return {
            fields,
            confirmDelete,
            handleConfirmDelete,
            handleClickDelete,
            Inertia,
            navLinks,
            param,
            role_query,
            getRoles,
        };
    },
});
</script>
