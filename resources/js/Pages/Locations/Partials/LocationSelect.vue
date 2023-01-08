<template>
    <ApolloQuery :query="(gql) => queries['locations']">
        <template v-slot="{ result: { loading, error, data }, isLoading }">
            <template v-if="isLoading">
                <p>Loading...</p>
            </template>

            <template v-if="error">
                <jet-input-error :message="error" class="mt-2" />
            </template>

            <template v-if="data">
                <jet-label for="location" value="Locations" />
                <Multiselect
                    v-bind="$attrs"
                    id="location"
                    class="mt-1 multiselect"
                    mode="tags"
                    v-model="localValue"
                    :close-on-select="false"
                    :create-option="true"
                    :options="formatLocationSelect(data.locations.data)"
                    :classes="getDefaultMultiselectTWClasses()"
                    :valueProp="'value'"
                    :label="'label'"
                />
            </template>
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
        type: [String, Array, Object],
        default: "",
    },
});

const formatLocationSelect = (data) => {
    if (!data instanceof Array) return [];
    return data.map((location) => {
        return {
            value: location.id,
            label: location.name,
        };
    });
};

const localValue = ref(props.modelValue);
const emit = defineEmits(["update:modelValue"]);

watch(localValue, (nv, ov) => emit("update:modelValue", nv));
</script>
