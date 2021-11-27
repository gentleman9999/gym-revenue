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
                    <jet-label for="name" value="Name"/>
                    <input id="name" type="text" class="block w-full mt-1" v-model="form.name" autofocus/>
                    <jet-input-error :message="form.errors.name" class="mt-2"/>
                </div>
                <div>
                    Drag Files To Upload
                </div>


                <input id="client_id" type="hidden" v-model="form.client_id"/>
                <jet-input-error :message="form.errors.client_id" class="mt-2"/>
        </template>

        <template #actions>
<!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
            <button type="button" @click="$inertia.visit(route('files'))" :class="{ 'opacity-25': form.processing }" error outline :disabled="form.processing">
                Cancel
            </button>
            <div class="flex-grow" />
            <button :class="{ 'opacity-25': form.processing }" :disabled="form.processing"  :loading="form.processing">
                {{ buttonText }}
            </button>
        </template>
    </jet-form-section>
</template>

<script>
import {useForm} from '@inertiajs/inertia-vue3'

import AppLayout from '@/Layouts/AppLayout.vue'
import Button from '@/Components/Button.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },
    props: ['clientId', 'location'],
    setup(props, context) {

        const form = useForm({
            files: null
        })

        let handleSubmit = () => form.post(`/files`);

        return {form, buttonText: 'Save', handleSubmit}
    },
}
</script>
