<template>
    <AuthenticatedLayout>
      <div>
        <h1>Calendrier des demandes de congé </h1>
        <FullCalendar :options="calendarOptions"></FullCalendar>
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import { ref, onMounted, watch } from 'vue';
  import FullCalendar from '@fullcalendar/vue3';
  import dayGridPlugin from '@fullcalendar/daygrid';
  import interactionPlugin from '@fullcalendar/interaction';
  import frLocale from '@fullcalendar/core/locales/fr'; // Import the French locale
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  
  // Assuming props are passed directly to the component
  const props = defineProps({
    approvedRequests: {
      type: Array,
      required: true
    }
  });
  
  // Darker pastel colors
  const darkerPastelColors = [
    '#FF7F7F', '#FFBF80', '#FFFF80', '#80FF9F', '#80CFFF', 
    '#A560C3', '#7DBA7C', '#D4BF4F', '#83B4E1', '#F5928F'
  ];
  const userColors = {};
  
  // Helper function to get a color for each user
  function getUserColor(userId) {
    if (!userColors[userId]) {
      const colorIndex = Object.keys(userColors).length % darkerPastelColors.length;
      userColors[userId] = darkerPastelColors[colorIndex];
    }
    return userColors[userId];
  }
  
  const calendarEvents = ref([]);
  const calendarOptions = ref({
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    locale: frLocale, // Set the locale to French
    events: calendarEvents.value,
  });
  
  onMounted(() => {
    console.log("Approved Requests:", props.approvedRequests); // Debug line
  
    calendarEvents.value = props.approvedRequests.map(request => ({
      title: request.user.name,
      start: request.start_date,
      end: request.end_date,
      backgroundColor: getUserColor(request.user.id),
      borderColor: getUserColor(request.user.id)
    }));
  
    console.log("Calendar Events:", calendarEvents.value); // Debug line
    calendarOptions.value.events = calendarEvents.value; // Update events
  });
  
  // Watch for changes in calendarEvents and update calendarOptions
  watch(calendarEvents, (newEvents) => {
    calendarOptions.value.events = newEvents;
  });
  </script>
  
  <style scoped>
  #calendar {
    max-width: 900px;
    margin: 40px auto;
  }
  </style>
  