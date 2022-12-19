<template>
    <daisy-modal id="editModal" ref="editModal" @close="editParam = null">
        <ApolloQuery
            :query="(gql) => queries[modelKey].edit"
            :variables="editParam"
            v-if="editParam"
        >
            <template v-slot="{ result: { data, loading, error }, isLoading }">
                <div v-if="isLoading">
                    <spinner />
                </div>
                <div v-else-if="error">Error</div>
                <component
                    v-else-if="data"
                    :is="editComponent"
                    :editParam="editParam"
                    v-bind="{ ...data }"
                    @close="close"
                />
                <div v-else>No result</div>
            </template>
        </ApolloQuery>
    </daisy-modal>
</template>

<script setup>
import DaisyModal from "@/Components/DaisyModal.vue";
import Spinner from "@/Components/Spinner.vue";
import { ref, watchEffect, onUnmounted } from "vue";
import {
    editParam,
    clearEditParam,
    crudName,
} from "@/Components/CRUD/helpers/gqlData";
import queries from "@/gql/queries";

const props = defineProps({
    editComponent: {
        required: true,
    },
    modelName: {
        type: String,
        required: true,
    },
    modelKey: {
        type: String,
        required: true,
    },
});

const editModal = ref(null);

function open() {
    editModal?.value?.open();
}

const emit = defineEmits(["refresh"]);
function close() {
    emit("refresh");
    clearEditParam();
}

watchEffect(() => {
    if (props.modelName !== crudName.value) {
        return;
    }

    if (editParam.value) {
        open();
    } else {
        editModal?.value?.close();
    }
});
onUnmounted(() => {
    clearEditParam();
});
</script>

<style scoped></style>
