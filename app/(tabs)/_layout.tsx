import { Stack } from 'expo-router';

export default function Layout() {
  return (
    <Stack
      screenOptions={{
        // 1. Hide the default header (you built your own)
        headerShown: false,
        // 2. Change the transition to fade for a smooth feel
        animation: 'fade', 
        // 3. This makes the background color consistent during transition
        contentStyle: { backgroundColor: '#F8FAFC' }, 
      }}
    >
      {/* These names match your file names index.tsx and map.tsx */}
      <Stack.Screen name="index" />
      <Stack.Screen name="map" />
    </Stack>
  );
}