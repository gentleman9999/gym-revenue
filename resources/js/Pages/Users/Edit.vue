<template>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <ApolloQuery :query="queries.user.edit" :variables="{ id }">
            <template v-slot="{ result: { data } }">
                <LayoutHeader title="Edit User">
                    <jet-bar-icon type="goback" fill />
                    <h2
                        class="font-semibold text-xl leading-tight"
                        v-if="data?.user"
                    >
                        Edit {{ data.user.name }}
                    </h2>
                </LayoutHeader>
                <user-form
                    v-if="data?.user"
                    v-bind="{ ...data }"
                    :upload-file-route="uploadFileRoute"
                />
            </template>
        </ApolloQuery>
    </div>
    <!--            <user-form-->
    <!--                :is-client-user="$page.props.user.current_team?.isClientTeam"-->
    <!--                :roles="roles"-->
    <!--                :locations="locations"-->
    <!--                :available-departments="availableDepartments"-->
    <!--                :available-positions="availablePositions"-->
    <!--            />-->
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import UserForm from "@/Pages/Users/Partials/UserForm.vue";
import queries from "@/gql/queries";

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    uploadFileRoute: {
        type: Object,
        required: true,
    },
});
</script>
