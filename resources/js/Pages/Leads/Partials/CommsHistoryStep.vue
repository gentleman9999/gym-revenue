<template>
    <li class="step step-primary hover:bg-base-100 py-4 cursor-pointer w-32">
        <div class="after">
            <font-awesome-icon :icon="icon"/>
        </div>
        <div>
            <p>{{ heading }}</p>
        </div>
    </li>
</template>
<style scoped>
.step::after {
    content: unset;
}

.after {
    --tw-bg-opacity: 1;
    background-color: hsla(var(--p)/var(--tw-bg-opacity, 1));
    --tw-text-opacity: 1;
    color: hsla(var(--pc)/var(--tw-text-opacity, 1));
}

.after {
    z-index: 1;
    --tw-bg-opacity: 1;
    @apply bg-primary;
    border-radius: 9999px;
    display: block;
    display: grid;
    place-items: center;
    place-self: center;
    height: 2rem;
    position: relative;
    --tw-text-opacity: 1;
    color: hsla(var(--bc)/var(--tw-text-opacity, 1));
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
            type: Object
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
        }

        return {heading, icon}
    }
}
</script>
