<template>
    <form class="col-span-3" @submit.prevent="submit">
        <div>
            <slot/>
        </div>

        <div class="flex flex-row">
            <slot name="buttons"/>
            <div class="mr-4">
                <jet-button type="submit" success ><i class="fad fa-books-medical"></i>
                    {{ submitText }}
                </jet-button>
            </div>
            <div class="mr-4">
                <jet-button type="button" error @click="form.reset()"><i class="fad fa-trash"></i> Clear</jet-button>
            </div>
        </div>
    </form>
</template>

<script>
import {computed, defineComponent} from 'vue'
;
import JetButton from '@/Jetstream/Button.vue';
import {useForm} from '@inertiajs/inertia-vue3'


export default defineComponent({
    components: {

        JetButton,
    },
    props: {
        leadId: {
            type: String,
            required: true
        },
        submitText:{
            type: String,
            default: 'Submit'
        },
        form:{
            type: Object,
            required: true,
        }
    },
    emits: ["done"],
    setup(props, {emit}){
        const submit = async () => {
            await props.form.post(route('data.leads.contact', props.leadId));
            props.form.reset();
            emit('done');
            //TODO: we should have a NOTY provider or a way to set defaults instead of specifying theme everywhere
            new Noty({
                type: 'success',
                theme: 'sunset',
                text: 'Great Success!',
                timeout: 7500
            }).show();

        };
        return {submit}
    }
});
</script>

