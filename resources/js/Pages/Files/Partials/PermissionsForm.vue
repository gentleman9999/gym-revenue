<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <jet-label for="filename" value="Current File Permissions" />
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
                <jet-label for="admin" value="Admin" />
                <input
                    id="admin"
                    type="checkbox"
                    v-model="form.admin"
                    checked
                />
                <jet-input-error :message="form.errors.admin" class="mt-2" />
            </div>

            <div class="col-span-6">
                <jet-label for="account_owner" value="Account Owner" />
                <input
                    id="account_owner"
                    type="checkbox"
                    v-model="form.account_owner"
                />
                <jet-input-error :message="form.errors.account_owner" class="mt-2" />
            </div>

            <div class="col-span-6">
                <jet-label for="regional_admin" value="Regional Admin" />
                <input
                    id="regional_admin"
                    type="checkbox"
                    v-model="form.regional_admin"
                />
                <jet-input-error :message="form.errors.regional_admin" class="mt-2" />
            </div>

            <div class="col-span-6">
                <jet-label for="location_manager" value="Location Manager" />
                <input
                    id="location_manager"
                    type="checkbox"
                    v-model="form.location_manager"
                />
                <jet-input-error :message="form.errors.location_manager" class="mt-2" />
            </div>

            <div class="col-span-6">
                <jet-label for="employee" value="Employee" />
                <input
                    id="employee"
                    type="checkbox"
                    v-model="form.employee"
                />
                <jet-input-error :message="form.errors.employee" class="mt-2" />
            </div>

            <input id="client_id" type="hidden" v-model="form.client_id" />
            <jet-input-error :message="form.errors.client_id" class="mt-2" />
        </template>

        <template #actions>
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
import {Inertia} from "@inertiajs/inertia";

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
        let permissions = {
            id: null,
            admin: null,
            account_owner: null,
            regional_admin: null,
            location_manager: null,
            employee: null,
        }

        console.error(props.file);
        const form = useForm(permissions);

        let handleSubmit = async () => {
                let permissions = {
                    id: props.file.id,
                    admin: form.admin,
                    account_owner: form.account_owner,
                    regional_admin: form.regional_admin,
                    location_manager: form.location_manager,
                    employee: form.employee,
                }
            await Inertia.put(
                route("files.update", props.file.id),
                permissions
            );
            emit("success");
        };

        return {
            form,
            buttonText: "Update",
            handleSubmit,
            urlPrev,
        };
    },
};
</script>
