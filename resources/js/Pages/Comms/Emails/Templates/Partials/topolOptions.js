import theme from "@/theme.js";
const tailwindColors = theme.colors;
const daisyuiColors = theme.daisyui.themes[0].dark;

export const topolOptions = {
    customFileManager: true,
    disableAlerts: true,
    windowBar: ["fullscreen", "close"],
    topBarOptions: ["undoRedo", "changePreview", "previewSize", "save"],
    theme: {
        preset: "dark",
        borderRadius: {
            small: "4px",
            large: "8px",
        },
        colors: {
            900: tailwindColors.neutral[900],
            800: tailwindColors.neutral[850],
            700: tailwindColors.neutral[800],
            600: tailwindColors.neutral[750],
            500: tailwindColors.neutral[700],
            400: tailwindColors.neutral[600],
            350: tailwindColors.neutral[500],
            300: tailwindColors.neutral[450],
            200: tailwindColors.neutral[300],
            primary: daisyuiColors.primary,
            "primary-light": daisyuiColors["primary-focus"],
            "primary-dark": daisyuiColors["primary-content"],
            secondary: daisyuiColors.secondary,
            "secondary-light": daisyuiColors["secondary-focus"],
            error: daisyuiColors.error,
            "error-light": daisyuiColors.warning,
            success: daisyuiColors.success,
            "success-light": daisyuiColors["secondary-content"],
            active: daisyuiColors.secondary,
            "active-light": daisyuiColors["secondary-focus"],
        },
    },
    //this could be cool to add in a clients plans. we just need to hide it until we know how to
    //tap into the API to pull in the plans
    contentBlocks: {},
    savedBlocks: [],
    api: {
        // Your own endpoint for uploading images
        IMAGE_UPLOAD: "/images/upload",
        // Your own endpoint for getting contents of folders
        FOLDERS: "/images/folder-contents",
        // Your own endpoint to retrieve base64 image edited by Image Editor
        IMAGE_EDITOR_UPLOAD: "/images/image-editor-upload",
        // Create Autosave
        // AUTOSAVE: "/autosave",
        // Retreive all autosaves
        // AUTOSAVES: "/autosaves",
        // Retreive an autosave
        // GET_AUTOSAVE: "/autosave/",
        // Retrieve products from feed
        PRODUCTS: "/products",
    },
    mergeTags: [
        {
            name: "Tags", // Group name
            items: [],
        },
    ],
};
