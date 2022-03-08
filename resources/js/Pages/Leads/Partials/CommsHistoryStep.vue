<template>
    <li v-if="heading" class="step step-primary hover:bg-base-100 py-4 cursor-pointer w-32 rounded transition-colors duration-300 ease-in-out" :class="{active}">
        <div class="after">
            <font-awesome-icon :icon="icon"/>
        </div>
        <div>
            <p class="opacity-50">{{ heading }}</p>
            <p class="font-bold">{{ date }}</p>
        </div>
    </li>
</template>
<style scoped>
.step.active{
    @apply bg-success;
}
.step::after {
    content: unset;
}

.after {
    @apply bg-primary text-primary-content
}

.after {
    @apply bg-primary rounded-full grid relative text-base-content;
    z-index: 1;
    place-items: center;
    place-self: center;
    height: 2rem;
    width: 2rem;
    grid-column-start: 1;
    grid-row-start: 1;
}
</style>
<script>
import {computed} from 'vue';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {
    faChevronDoubleDown,
    faComment,
    faEnvelope,
    faPhoneAlt,
    faUserEdit,
    faUserPlus,
    faDoorOpen,
    faDollarSign,
    faStickyNote
} from '@fortawesome/pro-solid-svg-icons';
import {library} from "@fortawesome/fontawesome-svg-core";

library.add(faChevronDoubleDown, faUserPlus, faComment, faEnvelope, faUserEdit, faPhoneAlt, faDoorOpen, faDollarSign, faStickyNote);

export default {
    components: {
        FontAwesomeIcon
    },
    props: {
        detail: {
            type: Object,
            required: true
        },
        active: {
            type: Boolean,
            default: false,
        }
    },
    setup(props) {
        const heading = computed(() => {
            switch (props.detail.field) {
                case "called_by_rep":
                    return 'Phone Call';
                case "emailed_by_rep":
                    return 'Email';
                case "sms_by_rep":
                    return 'Text Message';
                case "claimed":
                    return 'Claimed';
                case "created":
                    return 'Created';
                case "updated":
                    return 'Updated';
                case "manual_create":
                    return "Created";
                case "trial-started":
                    return "Trial Started";
                case "trial-used":
                    return "Trial Used";
                case "note_created":
                    return "Note Created";
            }
        })
        const icon = computed(() => {
            switch (props.detail.field) {
                case "called_by_rep":
                    return 'phone-alt'
                case "emailed_by_rep":
                    return 'envelope';
                case "sms_by_rep":
                    return 'comment';
                case "claimed":
                    return 'chevron-double-down';
                case "manual_create":
                case "created":
                    return "user-plus";
                case "updated":
                    return 'user-edit';
                case "trial-started":
                    return "dollar-sign";
                case "trial-used":
                    return "door-open";
                case "note_created":
                    return "sticky-note"
            }
        })

        const date = computed(() => new Date(props.detail.created_at).toLocaleString());

        return {heading, icon, date}
    }
}
</script>
