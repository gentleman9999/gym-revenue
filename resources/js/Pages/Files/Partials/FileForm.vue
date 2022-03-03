<template>
    <jet-form-section @submitted="handleSubmit">
        <!--        <template #title>-->
        <!--            Location Details-->
        <!--        </template>-->

        <!--        <template #description>-->
        <!--            {{ buttonText }} a location.-->
        <!--        </template>-->
        <template #form>
            <div class="col-span-6">
                <jet-label for="filename" value="Current Filename" />
                <a
                    :href="file.url"
                    :download="file.filename"
                    target="_blank"
                    class="link link-hover"
                    >{{ file.filename }}</a
                >
                <jet-input-error :message="form.errors.file" class="mt-2" />
            </div>

            <div class="col-span-6">
                <jet-label for="filename" value="New Filename" />
                <input
                    id="filename"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.filename"
                    autofocus
                />
                <jet-input-error :message="form.errors.filename" class="mt-2" />
            </div>

            <input id="client_id" type="hidden" v-model="form.client_id" />
            <jet-input-error :message="form.errors.client_id" class="mt-2" />
        </template>

        <template #actions>
            <!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
            <!--            <Button-->
            <!--                type="button"-->
            <!--                @click="$inertia.visit(route('files'))"-->
            <!--                :class="{ 'opacity-25': form.processing }"-->
            <!--                error-->
            <!--                outline-->
            <!--                :disabled="form.processing"-->
            <!--            >-->
            <!--                Cancel-->
            <!--            </Button>-->
            <div class="flex-grow" />
            <Button
                class="btn-secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                :loading="form.processing"
            >
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import { useForm, usePage } from "@inertiajs/inertia-vue3";

import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
    },
    props: ["clientId", "file"],
    emits: ["success"],
    setup(props, { emit }) {
        let urlPrev = usePage().props.value.urlPrev;
        let file = props.file;

        const form = useForm(file);

        let handleSubmit = async () => {
            await form.put(route("files.update", file.id));
            emit("success");
        };

        return { form, buttonText: "Update", handleSubmit, urlPrev };
    },
};
</script>
