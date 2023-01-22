<template>
    <div v-if="locationsLoading">
        <span class="block py-4">Loading...</span>
    </div>
    <div v-if="!locationsLoading && !!resources">
        <jet-label for="club_id" value="Club" />
        <select
            v-model="localValue"
            v-bind="$attrs"
            class="w-full"
            id="club_id"
            :required="required"
        >
            <option disabled value="" selected>Select a Club</option>
            <option
                v-for="location in resources.data"
                :key="location.id"
                :value="location.id"
                :selected="location.id === localValue"
            >
                {{ location.name }}
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
const locationsLoading = ref(true);

const { result, loading, error, refetch } = useQuery(
    queries["locations"],
    props.param ? props.param : param,
    { throttle: 500 }
);

const resources = computed(() => {
    if (result.value && result.value["locations"]) {
        return _.cloneDeep(result.value["locations"]);
    } else return null;
});

watch(loading, (nv, ov) => {
    if (!mountingFinished.value) return;
    if (!!resources?.value) {
        locationsLoading.value = false;
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
    locationsLoading.value = false;
});
</script>
