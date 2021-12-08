<template>
    <div class="dropdown dropdown-end">
        <slot name="trigger">
            <button class="btn btn-ghost">
                <font-awesome-icon icon="ellipsis-h" size="lg" />
            </button>
        </slot>
        <slot name="before" />
        <slot>
            <ul
                tabindex="0"
                class="p-2 shadow menu dropdown-content bg-base-300 rounded-box w-52"
            >
                <li v-for="action in Object.values(actions)">
                    <a
                        @click.prevent="() => action.handler({ data, baseRoute })"
                        href="#"
                        >{{ action.label }}</a
                    >
                </li>
            </ul>
        </slot>
        <slot name="after" />
    </div>
</template>
<script>
import { defineComponent } from "vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faEllipsisH } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { isObject, merge, assign } from "lodash";
import { defaults as defaultActions } from "./helpers/actions";
library.add(faEllipsisH);

export default defineComponent({
    props: {
        actions: {
            type: Object,
            default: {},
        },
        data: {
            type: Object,
            required: true,
        },
        baseRoute: {
            type: String,
            required: true,
        },
    },
    components: {
        FontAwesomeIcon,
    },
    setup(props) {
        let __actions = Object.values(merge(defaultActions, props.actions))
            .filter((action) => action)
            .filter((action) =>
                action?.shouldRender ? action.shouldRender(props) : true
            );
        return { actions: __actions };
    },
});
</script>
