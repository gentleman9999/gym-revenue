<template>
    <email-builder />
    <daisy-modal>
        <div class="form-control">
            <label for="name" class="label">Name</label>
            <input type="text" v-model="form.name" autofocus id="name" />
            <jet-input-error :message="form.errors.name" class="mt-2" />
        </div>
        <!--        TODO: remove subject from email templates. templates should just be name, json, html, and thumbnail img-->
        <!--        <div class="form-control">-->
        <!--            <label for="subject" class="label">Subject Line</label>-->
        <!--            <input-->
        <!--                type="text"-->
        <!--                v-model="form.subject"-->
        <!--                id="subject"-->
        <!--                class="form-control w-full"-->
        <!--            />-->
        <!--            <jet-input-error :message="form.errors.subject" class="mt-2" />-->
        <!--        </div>-->
        <!--        <div class="form-control col-span-6 flex flex-row" v-if="canActivate">-->
        <!--            <input-->
        <!--                type="checkbox"-->
        <!--                v-model="form.active"-->
        <!--                autofocus-->
        <!--                id="active"-->
        <!--                class="mt-2"-->
        <!--                :value="true"-->
        <!--            />-->
        <!--            <label for="active" class="label ml-4"-->
        <!--                >Activate (allows assigning to Campaigns)</label-->
        <!--            >-->
        <!--            <jet-input-error :message="form.errors.active" class="mt-2" />-->
        <!--        </div>-->
        <template #actions>
            <!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
            <Button
                type="button"
                @click="$inertia.visit(route('comms.email-templates'))"
                :class="{ 'opacity-25': form.processing }"
                error
                outline
                :disabled="form.processing"
            >
                Cancel
            </Button>
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
    </daisy-modal>
</template>

<script>
import { ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import SweetModal from "@/Components/SweetModal3/SweetModal";
import EmailBuilder from "@/Pages/Comms/Emails/Templates/Partials/EmailBuilder";

export default {
    components: {
        EmailBuilder,
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        SweetModal,
        DaisyModal:
    },
    props: ["clientId", "template", "canActivate"],
    setup(props, context) {
        console.log("process.env", process.env);
        const modal = ref(null);
        let template = props.template;
        let operation = "Update";

        const form = useForm(template);

        let handleSubmit = () =>
            form.put(route("comms.email-templates.update", template.id));
        if (operation === "Create") {
            handleSubmit = () =>
                form.post(route("comms.email-templates.store"), {
                    headers: { "X-Inertia-Modal-Redirect": true },
                });
        }

        const handleOnSave = (args) => {
            console.log("handleOnSave", args);
        };

        return {
            form,
            buttonText: operation,
            handleSubmit,
            modal,
            handleOnSave,
        };
    },
};
</script>
