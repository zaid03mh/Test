<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';
import NavLink from '@/Components/NavLink.vue';

const showingNavigationDropdown = ref(false);
const notifications = ref([]);
const showNotifications = ref(false);
const unreadCount = ref(0);

// Fetch notifications on component mount
onMounted(() => {
    fetchNotifications();
});

const fetchNotifications = async () => {
    try {
        const response = await axios.get('/notifications');
        notifications.value = response.data;
        unreadCount.value = notifications.value.filter(n => !n.read_at).length;
        console.log('Notifications fetched:', notifications.value);
        console.log('Unread count:', unreadCount.value);
    } catch (error) {
        console.error('Failed to fetch notifications:', error);
    }
};

const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value;
    if (showNotifications.value) {
        markAllAsRead();
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post('/notifications/mark-as-read');
        unreadCount.value = 0;
        notifications.value.forEach(n => n.read_at = new Date().toISOString());
    } catch (error) {
        console.error('Failed to mark notifications as read:', error);
    }
};

const redirectToDetails = (url) => {
    Inertia.visit(url);
};

</script>

<template>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <div class="main-container">
        <div class="navbar">
            <img class="logonav" src="/build/assets/images/logo1.png">
            <div class="notifications" @click="toggleNotifications">
                <i class="fa fa-bell"></i>
                <span class="badge" v-if="unreadCount > 0">{{ unreadCount }}</span>
            </div>
            <div class="notification-dropdown" v-if="showNotifications">
                <ul>
                    <li v-for="notification in notifications" :key="notification.id">
                        <a href="#" @click.prevent="redirectToDetails(notification.data.url)">
                            {{ notification.data.message }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="sidebar">
            <div class="profile">
                <img src="/build/assets/images/profil1.png">
                <NavLink class="profile-name" :href="route('profile.edit')">{{ $page.props.auth.user.name }}</NavLink>
            </div>
            <ul>
                <li>
                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                        <i class="fa fa-home"></i> Dashboard
                    </NavLink>
                </li>
                <li>
                    <NavLink :href="route('leave-requests/create')">
                        <i class="fa fa-pen"></i> faire une demande
                    </NavLink>
                </li>
                <li>
                    <NavLink :href="route('leave-requests.index')">
                        <i class="fa fa-list"></i> Historique
                    </NavLink>
                </li>
                <li>
                    <NavLink :href="route('logout')" method="post">
                        <i class="fa fa-sign-out-alt"></i> Déconnexion
                    </NavLink>
                </li>
            </ul>
        </div>
        <div class="content">
            <slot></slot>
        </div>
    </div>
</template>

<style>
.main-container {
    display: flex;
    flex-direction: row;
    padding-top: 55px;
    height: 100vh;
    width: 100%;
}

.navbar {
    width: 100%;
    height: 65px;
    background-color: #ffffff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
}

.sidebar {
    width: 200px;
    background-color: #4CAF50;
    color: white;
    padding: 20px;
    box-sizing: border-box;
    overflow: auto;
    height: 100%;
}

.content {
    flex-grow: 1;
    background-color: #f8f9fa;
    padding: 20px;
    overflow: auto;
}

body, html {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    height: 100%;
    box-sizing: border-box;
}

.sidebar ul {
    list-style-type: none;
    padding: 0;
}

.sidebar li {
    margin: 10px 0;
    display: flex;
    align-items: center;
}

.sidebar a {
    color: white;
    display: flex;
    align-items: center;
    padding: 10px;
    text-decoration: none;
}

.sidebar a i {
    margin-right: 8px;
}

.logonav {
    height: 50px;
    width: auto;
    justify-content: start;
}
.profile {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 20px;
    box-sizing: border-box;
}

.profile img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-bottom: 10px;
}

.profile-name {
    color: white;
    text-decoration: none;
    font-size: 16px;
    text-align: center;
}
.notifications {
    position: relative;
    cursor: pointer;
}

.notifications .fa-bell {
    font-size: 20px;
    position: relative;
}

.notifications .badge {
    position: absolute;
    top: -10px;
    right: -10px;
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 3px 6px;
    font-size: 12px;
    line-height: 1;
    display: flex;
    justify-content: center;
    align-items: center;
}


.notification-dropdown {
    position: absolute;
    top: 65px;
    right: 20px;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 300px;
    z-index: 1000;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.notification-dropdown ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.notification-dropdown li {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.notification-dropdown li a {
    text-decoration: none;
    color: black;
    display: block;
}

.notification-dropdown li:last-child {
    border-bottom: none;
}


</style>
