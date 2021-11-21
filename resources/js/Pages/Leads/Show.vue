<template>
    <app-layout :title="title">
        <jet-bar-container>
            <lead-interaction :lead-id="lead.id"
                              :user-id="$page.props.user.id"
                              :first-name="lead.first_name"
                              :last-name="lead.last_name"
                              :email="lead.email"
                              :phone="lead.mobile_phone"
                              :details="lead['details_desc']"
            />
        </jet-bar-container>
    </app-layout>
</template>

<script>
import {defineComponent} from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import JetBarContainer from "@/Components/JetBarContainer";
import LeadInteraction from "./Partials/LeadInteractionContainer";


export default defineComponent({
    components:{
        AppLayout,
        JetBarContainer,
        LeadInteraction,
    },
    props: {
        lead:{
            type:Object,
            required: true
        }
    }
});
</script>

<style scoped>

</style>
