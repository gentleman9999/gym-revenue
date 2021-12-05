<template>
    <data-card>
        <template #title>
            <slot name="title">
                <div :title="__title">
                    {{ __title }}
                </div>
            </slot>
        </template>
        <template #actions>
            <slot name="actions">
                <div class="dropdown dropdown-end">
                    <button class="btn btn-ghost">
                        <font-awesome-icon
                            :icon="['fas', 'align-left']"
                            size="lg"
                        />
                    </button>
                    <ul
                        tabindex="0"
                        class="p-2 shadow menu dropdown-content bg-base-300 rounded-box w-52"
                    >
                        <li v-for="action in Object.values(__actions)">
                            <a @click.prevent="action.handler" href="#">{{ action.label }}</a>
                        </li>
                    </ul>
                </div>
            </slot>
        </template>
        <div v-for="(field, index) in __fields" class="col-span-3 truncate">
            <component v-if="fields?.component" :is="fields[index].component">
                {{ data[field] }}
            </component>
            <template v-else>
                <div class="text-xs text-gray-500">
                    {{ isObject(field) ? field.name : field }}
                </div>

                <vue-json-pretty
                    v-if="isObject(data[field])"
                    :data="data[field]"
                />
                <span
                    v-else
                    :title="
                        isObject(field)
                            ? field.transform(data[field.name])
                            : data[field]
                    "
                >
                    {{
                        isObject(field)
                            ? field.transform(data[field.name])
                            : data[field]
                    }}
                </span>
            </template>
        </div>
    </data-card>
</template>

<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faAlignLeft } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import DataCard from "./DataCard";
import { isObject, merge } from "lodash";
import { transforms, defaults as defaultTransforms } from "./transforms";
import VueJsonPretty from "vue-json-pretty";
import "vue-json-pretty/lib/styles.css";
import {Inertia} from "@inertiajs/inertia";

library.add(faAlignLeft);

export default {
    components: {
        FontAwesomeIcon,
        VueJsonPretty,
        DataCard,
    },
    props: {
        data: {
            type: Object,
            required: true,
        },
        fields: {
            type: Array,
            required: true,
        },
        update: {
            type: Boolean,
            default: true,
        },
        delete: {
            type: Boolean,
            default: true,
        },
        modelName: {
            type: String,
        },
        modelNamePlural: {
            type: String,
        },
        titleField: {
            type: String,
        },
        actions: {
            type: Object,
            default: {}
        },
        baseUrl:{
            type: String,
        }
    },
    setup(props) {
        let __fields;
        let __title;
        if (props.titleField) {
            __title = props.data[props.titleField];
        } else {
            __title = props.data.name || props.data.id;
        }

        if (props.fields) {
            __fields = props.fields.map((header) =>
                isObject(header) ? header.label : header
            );
        } else {
            //they didn't profile a fields object, so pick smart defaults
            __fields = Object.keys(props.data);
        }

        let titleKey = props.titleField;
        if (!titleKey) {
            titleKey = props.data.name ? "name" : "id";
        }

        if (__title && __fields.includes(titleKey)) {
            //if we use a default title field, don't display it twice
            console.log("here", __fields.indexOf(titleKey));
            __fields.splice(__fields.indexOf(titleKey), 1);
        }

        __fields = __fields.map((field) =>
            field in defaultTransforms
                ? { name: field, transform: defaultTransforms[field] }
                : field
        );

        let __baseUrl = props.baseUrl || props.modelNamePlural || props.modelName+ 's';
        let defaultActions = {
            edit: { label: "Edit", handler: () => {Inertia.visit(route(`${__baseUrl}.edit`, props.data.id))} },
            trash: { label: "Trash", handler: () => {Inertia.visit(route('files.trash', props.data.id))} },
        };
        let __actions = Object.values(merge(defaultActions, props.actions)).filter(action=>action);

        return { __fields, isObject, __title, __actions, __baseUrl };
    },
};
</script>
