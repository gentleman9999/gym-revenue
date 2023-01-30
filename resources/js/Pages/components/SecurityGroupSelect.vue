<template>
    <div v-if="groupsLoading">
        <span class="block py-4">Loading...</span>
    </div>
    <div v-if="!groupsLoading && !!resources">
        <jet-label for="sec_group" value="Security Group" />
        <select
            v-model="localValue"
            v-bind="$attrs"
            class="w-full"
            id="sec_group"
            :required="required"
        >
            <option disabled value="" selected>Select a Security Group</option>
            <option
                v-for="group in resources"
                :key="group.value"
                :value="group.value"
                :selected="group.value === localValue"
            >
                {{ group.name }}
            </option>
        </select>
    </div>
</template>

<script setup>
import { ref, defineEmits, watch, computed, onMounted } from "vue";
import queries from "@/gql/queries";
import JetLabel from "@/Jetstream/Label.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import { useQuery } from "@vue/apollo-composable";

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

const param = ref({
    page: 1,
});

const mountingFinished = ref(false);
const groupsLoading = ref(true);

const { result, loading, error, refetch } = useQuery(
    queries["securityGroups"],
    props.param ? props.param : param,
    { throttle: 500 }
);

const resources = computed(() => {
    if (result.value && result.value["securityGroups"]) {
        return _.cloneDeep(result.value["securityGroups"]);
    } else return null;
});

watch(loading, (nv, ov) => {
    if (!mountingFinished.value) return;
    if (!!resources?.value) {
        groupsLoading.value = false;
    }
});

const localValue = ref(props.modelValue);
const emit = defineEmits(["update:modelValue"]);

watch(localValue, async (nv, ov) => {
    await refetch();
    emit("update:modelValue", nv);
});

onMounted(async () => {
    await refetch();
    mountingFinished.value = true;
    groupsLoading.value = false;
});
</script>
