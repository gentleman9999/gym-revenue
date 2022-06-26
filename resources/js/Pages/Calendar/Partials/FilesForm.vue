<template>
    <form @submit="handleSubmit" class="w-full grid grid-cols-6 gap-4">
        <div class="col-span-6" v-if="form.attendees?.length">
            <table class="table table-compact w-full">
                <thead>
                    <tr>
                        <th></th>
                        <th>File</th>
                        <th>Size</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="file in form.files" :key="file.id">
                        <td>
                            <file-extension-icon
                                :extension="file.extension"
                                v-if="
                                    ![
                                        'jpg',
                                        'jpeg',
                                        'png',
                                        'svg',
                                        'webp',
                                    ].includes(file.extension)
                                "
                                class="h-16 w-16"
                            />
                        </td>
                        <td>
                            <div class="flex items-center space-x-3">
                                <div>
                                    <div class="font-bold">
                                        <a
                                            :href="file.url"
                                            :download="file.filename"
                                            target="_blank"
                                            class="link link-hover"
                                            >{{ file.filename }}</a
                                        >
                                    </div>
                                    <div class="text-sm opacity-50">
                                        {{ file.extension }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-xs">
                                Size:<span class="badge badge-ghost badge-sm">{{
                                    file.size
                                }}</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <input id="client_id" type="hidden" v-model="form.client_id" />
    </form>
</template>
<style>
input {
    @apply input input-sm;
}
input,
select,
textarea {
    @apply w-full;
}
label {
    @apply mb-1;
}
</style>

<script>
import { watchEffect } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import "@vuepic/vue-datepicker/dist/main.css";
import DaisyModal from "@/Components/DaisyModal";
import FileExtensionIcon from "@/Pages/Files/Partials/FileExtensionIcon";
import { useGymRevForm } from "@/utils";

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        DaisyModal,
        FileExtensionIcon,
    },
    props: ["calendar_event"],
    setup(props, { emit }) {
        let calendarEvent = props.calendar_event;

        const form = useGymRevForm(calendarEvent);
        watchEffect(() => {});
        return {
            form,
        };
    },
};
</script>
