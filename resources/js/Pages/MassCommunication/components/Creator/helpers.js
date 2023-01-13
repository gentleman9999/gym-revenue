import { ref } from "vue";

/** @type {Source} */
export const Isource = {
    id: 0,
    name: "",
    selected: false,
};

/** @type {Audience} */
export const Iaudience = {
    id: 0,
    title: "",
    sources: [{ ...Isource }],
    editable: 1,
};

/** @type {Template} */
export const ITemplate = {
    email: false,
    sms: false,
    cell: false,
    date: false,
    day_in_campaign: 0,
};

/**
 * Creates a new audience with supplied values or defaults if none supplied
 * @param {Audience?} audience - object to template the audience from (optional)
 * @returns {Audience}
 */
export const audienceItemTemplate = (audience = { ...Iaudience }) => {
    return {
        id: audience?.id || "", // only for client side collision prevention - generate id's on back end
        title: audience?.name || "",
        filters: audience?.filters || [],
        entity:
            audience?.entity ||
            "App\\Domain\\EndUsers\\Leads\\Projections\\Lead",
        editable: !!audience?.editable,
    };
};

/**
 * Creates a new source with supplied values or defaults if none supplied
 * @param {Source?} source - object to template the source from (optional)
 * @returns {Source}
 */
export const sourceItemTemplate = (source = { ...Isource }) => {
    return {
        id: source["id"] ? source.id : genId(),
        name: source["name"]
            ? source.name.replace("_", " ")
            : "Source Not Named",
        selected:
            typeof source["selected"] === "boolean" ? source.selected : false,
    };
};

/**
 * Creates a new template with supplied values or defaults if none supplied
 * @param {Template?} template - represents the selected templates and their properties for that day
 * @returns {Template}
 */
export const templateItemTemplate = (template = { ...ITemplate }) => {
    return {
        email: template.email,
        sms: template.sms,
        call: template.call || "",
        date: template.date || false,
        day_in_campaign: template.day_in_campaign || 0,
    };
};

export const genId = () => {
    return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, (c) => {
        let r = (Math.random() * 16) | 0,
            v = c == "x" ? r : (r & 0x3) | 0x8;
        return v.toString(16);
    });
};

/**
 * Transform each object within the array for component usage
 * @param {Source[]} src Source data
 * @returns {Source[]}
 */
export const transformSource = (src) => {
    return src.map((item) => {
        return sourceItemTemplate(item);
    });
};

/**
 * Transform each object within the array for component usage
 * @param {Audience[]} src
 * @returns {Audience[]}
 */
export const transformAudience = (src) => {
    return src.map((item) => {
        return audienceItemTemplate(item);
    });
};

/**
 * Transform each object within the array for component usage
 * @param {Template[]} src
 * @returns {Template}
 */
export const transformTemplate = (src) => {
    return src.map((item) => {
        return templateItemTemplate(item);
    });
};

/**
 * @typedef {Object} Audience
 * @property {number|string} id - unique id to avoid possible collisions
 * @property {string} title - a name given to this group of sources
 * @property {Array.<Source>} sources - list of the selected sources
 */

/**
 * @typedef {Object} Source
 * @property {number|string} id - unique id to avoid possible collisions
 * @property {string} name - the name of this individual source
 * @property {boolean} selected - whether this source is selected or not
 */

/**
 * @typedef {Object} Template Represents an event point you define through the properties.
 *  - multiday: interface represents one single day, which together form an array of which is an entire campaign.
 * @property {boolean|number|string} email - the id of the selected email template, or false if none selected
 * @property {boolean|number|string} sms - the id of the selected sms template, or false if none selected
 * @property {boolean|string} call - the script to use when calling, or false if not selected
 * @property {boolean|Date|string} [date] - the date for the scheduled campaign, if multiday campaign, this is false
 * @property {number} [day_in_campaign] - on which day of the campaign this day's events occur
 */

export const transformDayTemplate = (src) => {
    return {
        email:
            typeof src?.email_template_id === "string" &&
            src?.email_template_id !== "0"
                ? src?.email_template_id
                : false,
        sms:
            typeof src?.sms_template_id === "string" &&
            src?.sms_template_id !== "0"
                ? src?.sms_template_id
                : false,
        call:
            typeof src?.call_template_id === "string" &&
            src?.call_template_id !== "0"
                ? src?.call_template_id
                : false,
        day_in_campaign:
            typeof src?.day_of_campaign === "number"
                ? src.day_of_campaign
                : typeof src?.day_of_campaign === "string"
                ? parseInt(src.day_of_campaign)
                : 0,
        date: typeof src?.send_at === "string" ? src.send_at : false,
        id: typeof src?.id === "string" ? src.id : null,
    };
};

export const CURRENT_CAMPAIGNS = "current campaigns";
export const DRAFT_CAMPAIGNS = "draft campaigns";
export const ALL_CAMPAIGNS = "all campaigns";
export const FUTURE_CAMPAIGNS = "future campaigns";
export const COMPLETED_CAMPAIGNS = "completed campaigns";

export const FILTER = {
    CURRENT: CURRENT_CAMPAIGNS,
    DRAFT: DRAFT_CAMPAIGNS,
    ALL: ALL_CAMPAIGNS,
    FUTURE: FUTURE_CAMPAIGNS,
    COMPLETE: COMPLETED_CAMPAIGNS,
};

export const CAMPAIGN_FILTERS = [
    ALL_CAMPAIGNS,
    COMPLETED_CAMPAIGNS,
    CURRENT_CAMPAIGNS,
    DRAFT_CAMPAIGNS,
    FUTURE_CAMPAIGNS,
];

export const filterCampaigns = (campaigns = [], filter = ALL_CAMPAIGNS) => {
    if (filter === ALL_CAMPAIGNS) {
        return campaigns;
    } else if (filter === COMPLETED_CAMPAIGNS) {
        return campaigns.filter((campaign) => campaign.status === "COMPLETED");
    } else if (filter === FUTURE_CAMPAIGNS) {
        return campaigns.filter((campaign) => campaign.status === "PENDING");
    } else if (filter === CURRENT_CAMPAIGNS) {
        return campaigns.filter((campaign) => campaign.status === "ACTIVE");
    } else if (filter === DRAFT_CAMPAIGNS) {
        return campaigns.filter((campaign) => campaign.status === "DRAFT");
    }
};

/**
 * formats bytes into higher magnitudes and returns a string
 * such as: 7.02MB
 * @param {number} num - number of bytes to calculate
 */
export function formatBytes(num) {
    const units = ["B", "KB", "MB", "GB", "TB"];

    let bytes = num;
    let uind = 0;

    for (uind = 0; bytes >= 1024 && uind < 4; uind++) {
        bytes /= 1024;
    }

    return bytes.toFixed(2) + units[uind];
}
