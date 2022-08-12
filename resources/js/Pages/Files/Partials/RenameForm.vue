<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <jet-label
                    for="itemname"
                    :value="'Current ' + type + ' name'"
                />
                <a
                    :href="item.url"
                    :download="item.filename"
                    target="_blank"
                    class="link link-hover"
                    v-if="type === 'File'"
                >
                    {{ item.filename }}
                </a>
                <span v-else>{{ item.name }}</span>
                <jet-input-error :message="form.errors.item" class="mt-2" />
            </div>

            <div class="col-span-6">
                <jet-label for="itemname" :value="'New ' + type + ' Name'" />
                <input
                    id="itemname"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form[field]"
                    autofocus
                />
                <jet-input-error :message="form.errors[field]" class="mt-2" />
            </div>

            <input id="client_id" type="hidden" v-model="form.client_id" />
            <jet-input-error :message="form.errors.client_id" class="mt-2" />
        </template>

        <template #actions>
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
import { usePage } from "@inertiajs/inertia-vue3";
import { useGymRevForm } from "@/utils";

import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { computed } from "@vue/runtime-core";

export default {
    components: {
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
    },
    props: ["clientId", "item"],
    emits: ["success"],
    setup(props, { emit }) {
        let urlPrev = usePage().props.value.urlPrev;
        let item = props.item;

        const form = useGymRevForm(item);

        const type = computed(() => (item.filename ? "File" : "Folder"));
        const field = computed(() => (item.filename ? "filename" : "name"));

        let handleSubmit = async () => {
            if (type === "File") {
                await form.dirty().put(route("files.rename", item.id));
                emit("success");
            } else {
                // TODO rename folder
            }
        };

        return { form, buttonText: "Update", handleSubmit, urlPrev, type };
    },
};
</script>
