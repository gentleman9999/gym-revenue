<template>
    <li>
        <form @submit="handleSubmit">
            <div
                class="block focus:outline-none transition duration-150 ease-in-out relative bg-base-200 hover:bg-base-100 rounded-xl"
            >
                <progress
                    v-if="isUploading"
                    :value="uploadProgress"
                    max="100"
                    class="absolute inset-0 w-full h-full opacity-50"
                >
                    {{ uploadProgress }}%
                </progress>
                <div class="px-4 py-4 sm:px-6 relative z-1">
                    <div class="flex items-center justify-between">
                        <div
                            class="flex w-full items-center text-sm leading-5 font-medium text-secondary p-2 gap-4"
                        >
                            <file-extension-icon
                                :extension="file.type.split('/')[1]"
                                :size="file.size"
                            />

                            <input
                                type="text"
                                class="input-ghost input-sm w-full"
                                :value="form.filename"
                                @input="form.filename = $event.target.value"
                            />
                        </div>
                        <div class="hidden md:flex items-center gap-2">
                            <button
                                v-if="uploadProgress === -1"
                                class="badge badge-error"
                                @click="handleClickFailed"
                            >
                                Failed
                            </button>
                            <div
                                v-else
                                class="badge"
                                :class="{
                                    'badge-success': uploadProgress === 100,
                                    'badge-warning': uploadProgress < 100,
                                }"
                            >
                                {{
                                    uploadProgress < 100
                                        ? `${uploadProgress}%`
                                        : "Uploaded"
                                }}
                            </div>
                            <button
                                class="btn btn-ghost btn-error btn-circle"
                                @click.prevent="emit('remove', file)"
                            >
                                <font-awesome-icon icon="times" size="lg" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </li>
</template>

<style scoped>
progress[value]::-webkit-progress-bar {
    @apply bg-transparent;
}
progress::-webkit-progress-value {
    @apply bg-primary;
}
</style>
<script setup>
import { ref, computed, onMounted, watchEffect } from "vue";
import { useGymRevForm } from "@/utils";

import AppLayout from "@/Layouts/AppLayout";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import FileExtensionIcon from "./FileExtensionIcon";
import Vapor from "laravel-vapor";
import { Inertia } from "@inertiajs/inertia";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faTimes } from "@fortawesome/pro-solid-svg-icons";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

library.add(faTimes);

const props = defineProps({
    clientId: { type: String, required: true },
    entity_id: { type: String, required: false },
    userId: { type: String },
    file: { type: Object, required: true },
});

const emit = defineEmits(["remove"]);

const uploadProgress = ref(null);

const form = useGymRevForm({
    id: null,
    key: null,
    extension: null,
    bucket: null,
    filename: props.file.name,
    original_filename: props.file.name,
    client_id: props.clientId,
    user_id: props.userId,
    size: props.file.size,
    entity_id: props.entity_id,
    // is_public: true,
});

const removeFile = (file) => {
    console.log({ files: form.files, file });
    form.files = form.files.filter((f) => {
        return f !== file;
    });
};

// const handleSubmit = () => form.post(`/files`);
const handleSubmit = async () => {
    try {
        uploadProgress.value = 0;
        let response = await Vapor.store(props.file, {
            // visibility: form.isPublic ? 'public-read' : null,
            visibility: "public-read",
            progress: (progress) => {
                uploadProgress.value = Math.round(progress * 100);
            },
        });
        form.id = response.uuid;
        form.key = response.key;
        form.extension = response.extension;
        form.bucket = response.bucket;
    } catch (e) {
        console.error(e);
        uploadProgress.value = -1;
    }
};

const isUploading = computed(() => {
    return uploadProgress.value !== null && uploadProgress.value < 100;
});

const handleClickFailed = () => {
    if (uploadProgress.value === -1) {
        handleSubmit();
    }
};

onMounted(handleSubmit);

defineExpose({ form });
</script>
