<template>
    <ApolloQuery :query="(gql) => queries['genderTypes']">
        <template v-slot="{ result: { loading, error, data }, isLoading }">
            <template v-if="isLoading">
                <p>Loading...</p>
            </template>

            <template v-if="error">
                <jet-input-error :message="error" class="mt-2" />
            </template>

            <template v-if="data">
                <jet-label for="gender" value="Gender" />
                <select
                    v-bind="$attrs"
                    class="w-full"
                    id="gender"
                    :required="required"
                    v-model="localValue"
                >
                    <option value="">Select a Gender</option>
                    <option
                        v-for="gender in data.genders.data"
                        :key="gender.id"
                        :value="gender.id"
                    >
                        {{ gender.name }}
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
