import { supabase } from '@/src/services/supabase';
import { Feather } from '@expo/vector-icons';
import React, { useEffect, useRef, useState } from 'react';
import {
  Animated,
  SafeAreaView,
  ScrollView,
  StatusBar,
  Text,
  TouchableOpacity,
  View
} from 'react-native';
import BottomNavbar from '../components/BottomNavbar';
import { COLORS, IndexStyles as styles } from '../constants/theme';

const RECENT_ACTIVITY_DATA = [
  { id: '1', type: 'UPDATE', title: 'PHIVOLCS Bulletin #142', description: 'Sulfur dioxide emission averaged 1,432 tonnes/day.', time: '12m' },
  { id: '2', type: 'REPORT', title: 'Ashfall observed in Agoncillo', description: 'Light ashfall reported by 4 residents within 1km.', time: '38m' },
  { id: '3', type: 'SAFE', title: 'Evac center San Nicolas open', description: 'Capacity 240 · supplies stocked for 72hrs.', time: '1h' },
];

export default function DisasterAppUI() {
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const slideUpAnim = useRef(new Animated.Value(30)).current;
  const [userName, setUserName] = useState('User'); 

  useEffect(() => {
    Animated.parallel([
      Animated.timing(fadeAnim, { toValue: 1, duration: 600, useNativeDriver: true }),
      Animated.spring(slideUpAnim, { toValue: 0, friction: 6, useNativeDriver: true }),
    ]).start();

  const getUser = async () => {
    const {
      data: { user },
      error,
    } = await supabase.auth.getUser();

    if (error || !user) return;

    console.log(user); 

    setUserName(
      user.user_metadata?.full_name ||
      user.user_metadata?.name ||
      user.email?.split('@')[0] ||
      'User'
    );
  };

  getUser();
  }, []);

  return (
    <SafeAreaView style={styles.container}>
      <Animated.View style={{ flex: 1, opacity: fadeAnim, transform: [{ translateY: slideUpAnim }] }}>

        <StatusBar barStyle="dark-content" backgroundColor={COLORS.background} />

        <ScrollView
          contentContainerStyle={styles.scrollContent}
          showsVerticalScrollIndicator={false}
        >
          {/* --- Header --- */}
          <View style={styles.header}>
            <View>
              <Text style={styles.greetingText}>Hello,</Text>
              <Text style={styles.nameText}>{userName} 👋</Text>
            </View>
            <TouchableOpacity style={styles.notificationBtn}>
              <Feather name="bell" color={COLORS.primaryBlue} size={24} />
              <View style={styles.notificationDot} />
            </TouchableOpacity>
          </View>

          {/* --- Status Card --- */}
          <View style={styles.statusCard}>
            <View style={styles.statusHeader}>
              <Feather name="shield" color={COLORS.white} size={16} />
              <Text style={styles.statusHeaderText}>CURRENT STATUS</Text>
            </View>
            <Text style={styles.volcanoName}>Taal Volcano</Text>
            <Text style={styles.alertLevel}>Alert Level 1 · Normal conditions</Text>

            <View style={styles.statusBadgesRow}>
              <View style={styles.statusBadge}>
                <Text style={styles.statusBadgeText}>Updated 2 min ago</Text>
              </View>
              <View style={styles.statusBadge}>
                <Feather name="wind" color={COLORS.white} size={14} style={{ marginRight: 4 }} />
                <Text style={styles.statusBadgeText}>SW wind</Text>
              </View>
            </View>
          </View>

          {/* --- Quick Actions --- */}
          <Text style={styles.sectionTitle}>QUICK ACTIONS</Text>
          <View style={styles.quickActionsContainer}>
            <TouchableOpacity style={styles.sosCard}>
              <View style={styles.sosIconContainer}>
                <Feather name="alert-circle" color={COLORS.white} size={28} />
              </View>
              <View>
                <Text style={styles.sosTitle}>Emergency{'\n'}SOS</Text>
                <Text style={styles.sosSub}>Press to alert responders</Text>
              </View>
            </TouchableOpacity>

            <View style={styles.rightActionsColumn}>
              <TouchableOpacity style={styles.actionCard}>
                <View style={styles.actionIconContainer}>
                  <Feather name="phone" color={COLORS.primaryBlue} size={20} />
                </View>
                <Text style={styles.actionTitle}>Hotlines</Text>
                <Text style={styles.actionSub}>911 · PHIVOLCS</Text>
              </TouchableOpacity>

              <TouchableOpacity style={styles.actionCard}>
                <View style={styles.actionIconContainer}>
                  <Feather name="plus-square" color={COLORS.primaryBlue} size={20} />
                </View>
                <Text style={styles.actionTitle}>Shelters</Text>
                <Text style={styles.actionSub}>3 nearby</Text>
              </TouchableOpacity>
            </View>
          </View>

          {/* --- Recent Activity --- */}
          <View style={styles.activityHeader}>
            <Text style={styles.sectionTitleDark}>Recent Activity</Text>
            <TouchableOpacity>
              <Text style={styles.seeAllText}>See all</Text>
            </TouchableOpacity>
          </View>

          {RECENT_ACTIVITY_DATA.map((item) => (
            <View key={item.id} style={styles.activityCard}>
              <View style={styles.activityCardHeader}>
                <View style={[
                  styles.activityBadge,
                  item.type === 'UPDATE' && { backgroundColor: COLORS.badgeUpdateBg },
                  item.type === 'REPORT' && { backgroundColor: COLORS.badgeReportBg },
                  item.type === 'SAFE' && { backgroundColor: COLORS.badgeSafeBg },
                ]}>
                  <Text style={[
                    styles.activityBadgeText,
                    item.type === 'UPDATE' && { color: COLORS.badgeUpdateText },
                    item.type === 'REPORT' && { color: COLORS.badgeReportText },
                    item.type === 'SAFE' && { color: COLORS.badgeSafeText },
                  ]}>
                    {item.type}
                  </Text>
                </View>
                <View style={styles.timeContainer}>
                  <Feather name="activity" color={COLORS.textMuted} size={14} style={{ marginRight: 4 }} />
                  <Text style={styles.timeText}>{item.time}</Text>
                </View>
              </View>
              <Text style={styles.activityTitle}>{item.title}</Text>
              <Text style={styles.activityDesc}>{item.description}</Text>
            </View>
          ))}

          <View style={{ height: 100 }} />
        </ScrollView>
      </Animated.View>

      <BottomNavbar activeTab="home" />
    </SafeAreaView>
  );
}
