<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="form-control col-span-6">
                <label for="name" class="label">Name</label>
                <input type="text" v-model="form.name" autofocus id="name" />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
            <div class="col-span-6">
                <label for="name" class="label">Script</label>
                <textarea
                    class="form-control w-full"
                    rows="4"
                    cols="40"
                    v-model="form.script"
                    id="script"
                />
                <jet-input-error :message="form.errors.script" class="mt-2" />
            </div>
            <div class="form-control col-span-6 flex flex-row">
                <input
                    type="checkbox"
                    v-model="form.use_once"
                    id="use_once"
                    class="mt-2"
                    :value="true"
                />
                <label for="use_once" class="label ml-4"
                    >Use Once (scipt can only be used once)</label
                >
                <jet-input-error :message="form.errors.use_once" class="mt-2" />
            </div>
            <div
                class="form-control col-span-6 flex flex-row"
                v-if="canActivate"
            >
                <input
                    type="checkbox"
                    v-model="form.active"
                    id="active"
                    class="mt-2"
                    :value="true"
                />
                <label for="active" class="label ml-4"
                    >Activate (allows assigning to Campaigns)</label
                >
                <jet-input-error :message="form.errors.active" class="mt-2" />
            </div>
            <!--                <input id="client_id" type="hidden" v-model="form.client_id"/>-->
            <!--                <jet-input-error :message="form.errors.client_id" class="mt-2"/>-->
        </template>

        <template #actions>
            <!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
            <Button
                v-if="useInertia"
                type="button"
                @click="handleCancel"
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
                :disabled="form.processing || !form.isDirty"
                :loading="form.processing"
            >
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import { useGymRevForm } from "@/utils";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import { Inertia } from "@inertiajs/inertia";

export default {
    components: {
        Button,
        JetFormSection,
        JetInputError,
    },
    props: {
        callTemplate: {
            type: Object,
        },
        duplicate: {
            type: Object,
        },
        canActivate: {
            type: Boolean,
            required: true,
        },
    },
    setup(props, { emit }) {
        let template = props.callTemplate;
        let duplicate = props.duplicate;
        console.log(duplicate);
        let operation = "Update";
        if (!template) {
            template = {
                script: "",
                name: "",
                active: false,
                use_once: false,
                json: [],
            };
            operation = "Create";
        }

        const form = useGymRevForm(template);

        let handleSubmit = () => {
            form.dirty().put(
                route("mass-comms.call-templates.update", template.id)
            );
        };
        if (operation === "Create") {
            if (duplicate) {
                operation = "Duplicate";
                form.script = duplicate.script;
                form.name = duplicate.name;
                form.use_once = duplicate.use_once ?? false;
                form.json = duplicate.json ?? [];
                form.thumnail = duplicate.thumnail;
                form.team_id = duplicate.team_id;
            }

            handleSubmit = () =>
                form.dirty().post(route("mass-comms.call-templates.store"));
        }

        const handleCancel = () => {
            console.warn('TODO: You need to emite a cancel event, and then close the daisy modal here (Inertia Modal was remove)');
            Inertia.visit(route("mass-comms.call-templates"));
        };
        return { form, buttonText: operation, handleSubmit, handleCancel };
    },
};
</script>
