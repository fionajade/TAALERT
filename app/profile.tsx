import { Feather } from '@expo/vector-icons';
import React, { useEffect, useRef } from 'react';
import {
  Animated,
  SafeAreaView,
  ScrollView,
  Text,
  TouchableOpacity,
  View
} from 'react-native';
import BottomNavbar from '../components/BottomNavbar';
import { ProfileStyles as styles } from '../constants/theme';

export default function ProfileScreen() {
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const slideUpAnim = useRef(new Animated.Value(30)).current;

  useEffect(() => {
    Animated.parallel([
      Animated.timing(fadeAnim, { toValue: 1, duration: 600, useNativeDriver: true }),
      Animated.spring(slideUpAnim, { toValue: 0, friction: 6, useNativeDriver: true }),
    ]).start();
  }, []);

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Profile</Text>
        <TouchableOpacity style={styles.settingsBtn}>
          <Feather name="settings" size={20} color="#1E293B" />
        </TouchableOpacity>
      </View>

      <Animated.View style={{ flex: 1, opacity: fadeAnim, transform: [{ translateY: slideUpAnim }] }}>
        <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
          
          {/* Main Profile Card */}
          <View style={styles.profileCard}>
            <View style={styles.avatar}>
              <Text style={styles.avatarText}>M</Text>
            </View>
            <View style={styles.profileInfo}>
              <Text style={styles.userName}>Marcus</Text>
              <View style={styles.locationRow}>
                <Feather name="map-pin" size={14} color="#64748B" />
                <Text style={styles.locationText}>Brgy. Banga, Talisay, Batangas</Text>
              </View>
              <View style={styles.badge}>
                <Text style={styles.badgeText}>VERIFIED RESIDENT</Text>
              </View>
            </View>
          </View>

          {/* Stats Row */}
          <View style={styles.statsRow}>
            <StatCard value="12" label="REPORTS" />
            <StatCard value="38" label="ALERTS" />
            <StatCard value="142" label="SAFE DAYS" />
          </View>

          {/* Menu List */}
          <View style={styles.menuContainer}>
            <MenuItem icon="bell" label="Notification preferences" color="#25A5FE" bg="#E0F2FE" />
            <MenuItem icon="shield" label="Emergency contacts" color="#25A5FE" bg="#E0F2FE" />
            <MenuItem icon="log-out" label="Sign out" color="#EF4444" bg="#FEE2E2" isLast />
          </View>

          <View style={{ height: 100 }} />
        </ScrollView>
      </Animated.View>

      <BottomNavbar activeTab="profile" />
    </SafeAreaView>
  );
}

// Stat Card
const StatCard = ({ value, label }: any) => (
  <View style={styles.statCard}>
    <Text style={styles.statValue}>{value}</Text>
    <Text style={styles.statLabel}>{label}</Text>
  </View>
);

// Menu Item
const MenuItem = ({ icon, label, sub, color, bg, isLast }: any) => (
  <TouchableOpacity style={[styles.menuItem, isLast && { borderBottomWidth: 0 }]}>
    <View style={[styles.menuIconBox, { backgroundColor: bg }]}>
      <Feather name={icon} size={18} color={color} />
    </View>
    <View style={styles.menuTextContainer}>
      <Text style={[styles.menuLabel, icon === 'log-out' && { color: '#EF4444' }]}>{label}</Text>
      {sub && <Text style={styles.menuSubText}>{sub}</Text>}
    </View>
    <Feather name="chevron-right" size={18} color="#CBD5E1" />
  </TouchableOpacity>
);
