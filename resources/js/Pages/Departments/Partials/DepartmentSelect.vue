<template>
    <ApolloQuery :query="(gql) => queries['departmentList']">
        <template v-slot="{ result: { loading, error, data }, isLoading }">
            <!-- this seems to work, while 'loading' does not -->
            <template v-if="isLoading">
                <p>Loading...</p>
            </template>

            <template v-if="error">
                <jet-input-error :message="error" class="mt-2" />
            </template>

            <template v-if="data">
                <jet-label for="departments" value="Select Department" />
                <Multiselect
                    v-bind="$attrs"
                    id="departments"
                    class="mt-1 multiselect"
                    v-model="localValue"
                    :searchable="true"
                    :options="formatDepartmentForSelect(data.departments.data)"
                    :classes="getDefaultMultiselectTWClasses()"
                />
            </template>

            <!-- <div v-else>No result</div> -->
        </template>
    </ApolloQuery>
</template>

<script setup>
import { ref, defineEmits, watch } from "vue";
import queries from "@/gql/queries";
import { getDefaultMultiselectTWClasses } from "@/utils";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import Multiselect from "@vueform/multiselect";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
});

const localValue = ref(props.modelValue);
const emit = defineEmits(["update:modelValue"]);

/**
 * Format department data type for multiselect component consumption
 */
const formatDepartmentForSelect = (data) => {
    console.log("got DEPARTMENTS:", data);
    if (!data instanceof Array || !data) return [];
    return data.map((dpt) => {
        return {
            value: dpt.id,
            label: dpt.name,
        };
    });
};

watch(localValue, (nv, ov) => emit("update:modelValue", nv));
</script>
