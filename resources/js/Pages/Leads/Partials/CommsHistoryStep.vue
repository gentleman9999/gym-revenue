<template>
    <li class="step step-primary hover:bg-base-100 py-4 cursor-pointer w-32">
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
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import { faChevronDoubleDown, faUserPlus, faUserEdit, faComment, faEnvelope, faPhoneAlt } from '@fortawesome/pro-solid-svg-icons';
import {library} from "@fortawesome/fontawesome-svg-core";
library.add(faChevronDoubleDown, faUserPlus, faComment,faEnvelope, faUserEdit, faPhoneAlt);

export default {
    components: {
        FontAwesomeIcon
    },
    props: {
        detail: {
            type: Object,
            required: true
        }
    },
    setup(props) {
        const field = props.detail.field;
        let heading = null;
        let icon = null;

        switch (field) {
            case "called_by_rep":
                heading = 'Phone Call';
                icon = 'phone-alt'
                break;
            case "emailed_by_rep":
                heading = 'Email';
                icon = 'envelope';
                break;
            case "sms_by_rep":
                heading = 'Text Message';
                icon = 'comment';
                break;
            case "claimed":
                heading = 'Claimed';
                icon='chevron-double-down';
                break;
            case "created":
                heading = 'Created';
                icon = "user-plus";
                break;
            case "updated":
                heading = 'Updated';
                icon = 'user-edit';
                break;
            case "manual_create":
                heading = "Created";
                break;
        }

        const date = new Date(props.detail.created_at).toLocaleString();

        return {heading, icon, date}
    }
}
</script>
