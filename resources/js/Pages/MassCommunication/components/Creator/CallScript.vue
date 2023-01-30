<template>
    <div
        class="bg-black p-4 border-secondary border rounded-md max-w-md w-full"
    >
        <div
            class="p-4 bg-secondary-content text-black border-secondary border rounded-md"
        >
            <h2 class="text-xl font-bold">Script for Phone Call</h2>
            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit.
                Eligendi,
            </p>

            <div class="flex flex-col">
                <label for="callscriptname" class="mt-4">Name</label>
                <input
                    id="callscriptname"
                    :disabled="isProcessing"
                    v-model="form.name"
                    placeholder="Call Script Name"
                    type="text"
                    class="border border-secondary bg-primary-content mt-1"
                />

                <label for="callscriptscript" class="mt-4">Script</label>
                <textarea
                    id="callscriptscript"
                    :disabled="isProcessing"
                    v-model="form.script"
                    rows="5"
                    class="border border-secondary bg-primary-content resize-none mt-1"
                ></textarea>
            </div>

            <div class="flex justify-between mt-4">
                <button
                    :disabled="isProcessing"
                    @click="$emit('cancel')"
                    class="disabled:cursor-not-allowed disabled:opacity-25"
                >
                    Cancel
                </button>
                <button
                    @click="handleOperation"
                    class="px-2 py-1 border-secondary border rounded-md bg-secondary transition-all text-base-content hover:bg-base-content hover:text-secondary disabled:opacity-25 disabled:cursor-not-allowed"
                    :disabled="isProcessing"
                    :loading="isProcessing"
                >
                    Save
                </button>
                <button
                    @click="handleOperation"
                    class="px-2 py-1 border-secondary border rounded-md bg-secondary transition-all text-base-content hover:bg-base-content hover:text-secondary disabled:opacity-25 disabled:cursor-not-allowed"
                    :disabled="isProcessing"
                >
                    One time use
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
input:disabled,
textarea:disabled {
    @apply bg-neutral bg-opacity-20 border border-secondary italic text-base-100 text-opacity-50;
}
</style>

<script setup>
import { ref, onMounted, computed } from "vue";
import { toastInfo, toastError } from "@/utils/createToast";
import { useGymRevForm } from "@/utils";
import { useMutation } from "@vue/apollo-composable";
import mutations from "@/gql/mutations";

const emit = defineEmits(["done", "cancel"]);
const props = defineProps({
    template: {
        type: [Object, null],
        default: {
            name: "",
            script: "",
            active: true,
        },
    },
    editParam: {
        type: [Object, null],
        default: null,
    },
});

const isProcessing = ref(false);

const { mutate: createCallTemplate } = useMutation(
    mutations.callTemplate.create
);
const { mutate: updateCallTemplate } = useMutation(
    mutations.callTemplate.update
);

const form = useGymRevForm(props.template);

let operation = computed(() => {
    return props.editParam !== null ? "Update" : "Create";
});

let operFn = computed(() => {
    return operation.value === "Update"
        ? updateCallTemplate
        : createCallTemplate;
});

const handleOperation = async () => {
    try {
        isProcessing.value = true;
        let rawData = {
            id: form?.id,
            name: form?.name,
            script: form?.script,
            use_once:
                typeof form?.use_once === "boolean" ? form.use_once : false,
            active: typeof form?.active === "boolean" ? form.active : false,
        };
        if (props.editParam === null) delete rawData.id;

        const { data } = await operFn.value({
            input: rawData,
        });

        let savedTemplate =
            data["createCallScriptTemplate"] ??
            data["updateCallScriptTemplate"];
        toastInfo("Call Template " + operation.value + "d!");
        emit("done", savedTemplate);
    } catch (error) {
        toastError("There was a problem updating the data - try again..");
        isProcessing.value = false;
        console.log("template operation error:", error);
    }
};
</script>
