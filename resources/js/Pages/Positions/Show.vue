<template>
    <LayoutHeader title="Positions" />
    <page-toolbar-nav title="Positions" :links="navLinks" />
    <ApolloQuery :query="(gql) => position_query" :variables="param">
        <template v-slot="{ result: { data } }">
            <gym-revenue-crud
                v-if="data"
                base-route="positions"
                model-name="Position"
                model-key="positions"
                :fields="fields"
                :resource="getPositions(data)"
                @update="handleCrudUpdate"
                :actions="{
                    trash: {
                        handler: ({ data }) => handleClickTrash(data),
                    },
                }"
            />
        </template>
    </ApolloQuery>

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
        const param = ref({
            page: 1,
        });
        const position_query = gql`
            query Positions($page: Int, $filter: Filter) {
                positions(page: $page, filter: $filter) {
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

        const getPositions = (data) => {
            return _.cloneDeep(data.positions);
        };
        const handleCrudUpdate = (key, value) => {
            if (typeof value === "object") {
                param.value = {
                    ...param.value,
                    [key]: {
                        ...param.value[key],
                        ...value,
                    },
                };
            } else {
                param.value = {
                    ...param.value,
                    [key]: value,
                };
            }
        };
        return {
            fields,
            confirmTrash,
            handleConfirmTrash,
            handleClickTrash,
            Inertia,
            navLinks,
            param,
            position_query,
            getPositions,
            handleCrudUpdate,
        };
    },
});
</script>
