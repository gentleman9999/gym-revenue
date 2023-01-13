<template>
    <ApolloQuery :query="(gql) => queries['audiences']">
        <template v-slot="{ result: { loading, error, data }, isLoading }">
            <template v-if="isLoading">
                <span class="text-black block py-4">Loading...</span>
            </template>

            <template v-if="error">
                <jet-input-error :message="error" class="mt-2" />
            </template>

            <template v-if="data">
                <slot name="label" />
                <select
                    v-bind="$attrs"
                    class="bg-base-content text-black p-2 rounded-none border border-black w-full mb-4"
                    id="audience"
                    :required="required"
                    v-model="localValue"
                >
                    <option
                        :disabled="true"
                        :value="null"
                        :selected="modelValue === '' || modelValue === null"
                    >
                        Select Audience
                    </option>
                    <option
                        v-for="audience in data.audiences.data"
                        :disabled="audience.title === 'Select an Audience'"
                        :key="audience.id"
                        :value="audience.id"
                    >
                        {{ audience.name }}
                    </option>
                </select>
            </template>
        </template>
    </ApolloQuery>
</template>

<script setup>
import { ref, defineEmits, watch } from "vue";
import queries from "@/gql/queries";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    required: {
        type: Boolean,
        default: false,
    },
});

const localValue = ref(props.modelValue);
const emit = defineEmits(["update:modelValue"]);

watch(localValue, (nv, ov) => emit("update:modelValue", nv));
</script>
