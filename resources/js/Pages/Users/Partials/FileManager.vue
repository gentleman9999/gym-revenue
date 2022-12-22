<template>
    <jet-form-section @submitted="handleSubmit">
        <template #description>
            <p class="font-bold mt-4">
                Ensure your document extension is '.CSV'.
            </p>
            <div class="divider"></div>
            <h1>
                If your CSV file doesn't contain a header row, please order your
                data based on the columns listed below.
            </h1>
            <p class="font-bold mt-4">Columns In Order</p>
            <ul class="list-decimal ml-4">
                <li>First Name</li>
                <li>Last Name</li>
                <li>Email</li>
            </ul>
        </template>
        <template #title> Import Users </template>
        <template #form>
            <section class="col-span-6 overflow-hidden">
                <div
                    class="max-w-screen-xl mx-auto text-center px-4 sm:px-6 lg:px-8"
                >
                    <div class="max-w-3xl mx-auto lg:max-w-none">
                        <div
                            class="mt-6 sm:mt-5 sm:grid sm:grid-cols-1 sm:gap-4 sm:items-start"
                        >
                            <div class="mt-2 sm:mt-0 sm:col-span-2">
                                <div
                                    v-cloak
                                    @dragover.prevent="
                                        onUploadDragoverEvent($event)
                                    "
                                    @drop.prevent="onUploadDropEvent($event)"
                                    class="w-full flex justify-center items-center px-6 pt-5 pb-6 border-2 border-gray-500 border-dashed rounded-md h-128 transition-colors"
                                    :class="{
                                        'bg-base-100': uploadDragoverTracking,
                                    }"
                                >
                                    <div class="relative z-20 text-center">
                                        <upload-icon
                                            :active="uploadDragoverTracking"
                                        />
                                        <p class="mt-4">
                                            Drag and Drop your Files here
                                        </p>
                                        <p class="py-2 text-gray-500">OR</p>
                                        <p class="mt-2 text-sm text-gray-600">
                                            <label class="btn btn-primary"
                                                ><span>Browse Files</span
                                                ><input
                                                    @input="
                                                        form.files = [
                                                            ...form.files,
                                                            ...$event.target
                                                                .files,
                                                        ]
                                                    "
                                                    type="file"
                                                    hidden
                                                    class="hidden"
                                                    accept=".csv"
                                            /></label>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <ul
                        class="shadow overflow-hidden sm:rounded-md mt-5 flex flex-col gap-2"
                    >
                        <file-upload-form
                            v-for="(file, i) in form.files"
                            :file="file"
                            :key="file"
                            :user-id="user?.id"
                            @remove="removeFile"
                            @input="fileUploadUpdated"
                            :ref="
                                (el) => {
                                    if (el) fileRefs[i] = el;
                                }
                            "
                        />
                    </ul>
                </div>
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </section>
        </template>

        <template #actions>
            <button
                type="button"
                @click="resolvedHandleCancel"
                :class="{ 'opacity-25': form.processing }"
                class="btn btn-error"
                error
                outline
                :disabled="form.processing || !form.isDirty"
            >
                Cancel
            </button>
            <div class="flex-grow" />
            <button
                class="btn btn-secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                :loading="form.processing"
            >
                Save
            </button>
        </template>
    </jet-form-section>
</template>

<script setup>
import { ref, computed, onBeforeUpdate, onUnmounted, defineEmits } from "vue";
import { useGymRevForm } from "@/utils";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import FileUploadForm from "@/Pages/Files/Partials/FileUploadForm.vue";
import UploadIcon from "@/Pages/Files/Partials/UploadIcon.vue";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    user: { type: Object },
    formSubmitOptions: { type: Object },
    handleCancel: { type: Function },
    uploadFileRoute: {
        type: Object,
        required: true,
    },
});

const defaultHandleCancel = () => {
    Inertia.visit(route("users"));
};
const resolvedHandleCancel = props.handleCancel || defaultHandleCancel;

const emit = defineEmits(["submitted"]);

const uploadDragoverTracking = ref(false);
const uploadDragoverEvent = ref(false);
const uploadProgress = ref(null);

const form = useGymRevForm({
    files: [],
});

const onUploadDragoverEvent = (e) => {
    uploadDragoverEvent.value = true;
    uploadDragoverTracking.value = true;
};
const onUploadDropEvent = (e) => {
    uploadDragoverEvent.value = false;
    uploadDragoverTracking.value = false;
    handleDroppedFiles(e);
};
const handleDroppedFiles = (e) => {
    let droppedFiles = e.dataTransfer.files;

    if (!droppedFiles) return;

    [...droppedFiles].forEach((f) => {
        form.files.push(f);
    });
};
const droppedFileValidator = (file) => {
    return false;
};
const removeFile = (file) => {
    console.log("removeFile", file);
    form.files = form.files.filter((f, i) => {
        const shouldKeep = f !== file;
        if (!shouldKeep) {
            console.log("about to splice fileRefs", fileRefs.value, i);
            fileRefs.value.splice(i, 1);
            fileRefs.value = [...fileRefs.value];
        }
        return shouldKeep;
    });
};

const fileRefs = ref([]);

onBeforeUpdate(() => {
    console.log("onBeforeUpdate");
    fileRefs.value = [];
});
const uploadedFiles = computed(() =>
    fileRefs.value.filter((ref) => {
        console.log({ form: ref.form, id: ref.form.id });
        return ref.form.id !== null && ref.form.id !== undefined;
    })
);
const numUploadedFiles = computed(() => uploadedFiles.value.length);
const numFiles = computed(() => fileRefs.value.length);
const formInvalid = computed(() => {
    if (numUploadedFiles.value !== numFiles.value) {
        return true;
    }
    return form.files.length === 0;
});

const allFiles = computed(() =>
    fileRefs.value.map((fileRef) => fileRef.form.data())
);

const formSubmitOptions = props?.formSubmitOptions || {};

const handleSubmit = () => {
    Inertia.post(route("users.import"), allFiles.value, {
        onSuccess: () => {
            emit("submitted");
            form.reset();
            fileRefs.value.forEach((fileRef) => fileRef.form.reset());
        },
        ...formSubmitOptions,
    });
};

const removeRouteGuard = Inertia.on("before", ({ detail: { visit } }) => {
    const { method } = visit;
    if (method === "get" && numUploadedFiles.value) {
        return confirm(
            "You haven't yet saved your uploaded files.  Are you sure you want to navigate away?"
        );
    }
});
defineExpose({ reset: form.reset });

onUnmounted(removeRouteGuard);
</script>
