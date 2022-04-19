const isToday = (date) => {
    const today = new Date();
    return (
        date.getDate() === today.getDate() &&
        date.getMonth() === today.getMonth() &&
        date.getFullYear() === today.getFullYear()
    );
};

const isThisWeek = (date) => {
    const todayObj = new Date();
    const todayDate = todayObj.getDate();
    const todayDay = todayObj.getDay();

    // get first date of week
    const firstDayOfWeek = new Date(todayObj.setDate(todayDate - todayDay));

    // get last date of week
    const lastDayOfWeek = new Date(firstDayOfWeek);
    lastDayOfWeek.setDate(lastDayOfWeek.getDate() + 6);

    // if date is equal or within the first and last dates of the week
    return date >= firstDayOfWeek && date <= lastDayOfWeek;
}

const getDayOfWeek = (date) => {
    switch (date.getDay()) {
        case 0:
            return "Sunday";
            break;
        case 1:
            return "Monday";
            break;
        case 2:
            return "Tuesday";
            break;
        case 3:
            return "Wednesday";
            break;
        case 4:
            return "Thursday";
            break;
        case 5:
            return "Friday";
            break;
        case 6:
            return "Saturday";
            break;
    }
};


export const parseNotificationResponse = (notification) => {
    console.log({ notification });
    let state = "info";
    let timeout = false;
    let text = notification.text;
    if (notification?.entity_type === "App\\Models\\Calendar\\CalendarEvent" && notification.entity) {
        const start = new Date(notification.entity?.start);
        const date = start.toLocaleDateString();
        const time = start.toLocaleTimeString();

        let start_at = '';
        if (isToday(start)) {
            start_at = `at ${time}`;
        } else if (isThisWeek(start)) {
            start_at =  `on ${getDayOfWeek(start)} at ${time}`;
        }
        start_at = `on ${date} at ${time}`;
        text = `${notification.entity.title} ${start_at}`;
    }
    return { text, state, timeout };
};
