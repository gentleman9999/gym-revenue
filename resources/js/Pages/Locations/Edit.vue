<template>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <ApolloQuery :query="queries.location.edit" :variables="{ id }">
            <template v-slot="{ result: { data } }">
                <LayoutHeader title="Edit Location">
                    <jet-bar-icon type="goback" fill />
                    <h2
                        class="font-semibold text-xl leading-tight"
                        v-if="data?.location"
                    >
                        Edit {{ data.location.name }} ({{
                            data.location["gymrevenue_id"]
                        }})
                    </h2>
                </LayoutHeader>
                <location-form
                    v-if="data?.location"
                    :client-id="this.$page.props.user.client_id"
                    v-bind="{ ...data }"
                />
            </template>
        </ApolloQuery>
    </div>
</template>

<script setup>
import LocationForm from "@/Pages/Locations/Partials/LocationForm.vue";
import { edit } from "@/Components/CRUD/helpers/gqlData";
import { onMounted, ref } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarIcon from "@/Components/JetBarIcon.vue";

import queries from "@/gql/queries";

const location = ref(null);

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
});
onMounted(() => {
    console.log("edit page ", { id: props.id });
    edit(props.id);
});
</script>
