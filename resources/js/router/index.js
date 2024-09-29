import { createRouter, createWebHistory } from "vue-router";
import HomePage from "../components/pages/HomePage.vue";
import StatsPage from "../components/pages/StatsPage.vue";
import NotFoundPage from "../components/pages/NotFoundPage.vue";

const routes = [
    { path: "/", component: HomePage },
    { path: "/stats", component: StatsPage },
    { path: "/:pathMatch(.*)*", component: NotFoundPage },
];

const router = createRouter({
    history: createWebHistory(),
    linkActiveClass: "active",
    routes,
});

export default router;
