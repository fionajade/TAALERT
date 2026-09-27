import { Feather } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React, { useEffect, useRef } from 'react';
import {
    Animated,
    SafeAreaView,
    ScrollView,
    Text,
    TouchableOpacity,
    View
} from 'react-native';
import { GuideStyles as styles } from '../constants/theme';

export default function EruptionGuideScreen() {
  const router = useRouter();
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
      {/* Header Area with Back Button */}
      <View style={styles.header}>
        <TouchableOpacity 
          style={styles.backBtn} 
          onPress={() => router.back()}
        >
          <Feather name="arrow-left" size={20} color="#25A5FE" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Eruption Safety</Text>
      </View>

      <Animated.View style={{ flex: 1, opacity: fadeAnim, transform: [{ translateY: slideUpAnim }] }}>
        <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
          
          <PhaseCard 
            title="Before an eruption"
            icon="alert-triangle"
            iconColor="#D97706"
            iconBg="#FEF3C7"
            tips={[
              "Know your local evacuation routes and shelters designated by authorities.",
              "Prepare an Emergency Go-Bag with 12 essentials including N95 masks and goggles.",
              "Monitor updates and alert levels from PHIVOLCS officially.",
              "Prepare an emergency communication plan with your family in case signals go down.",
              "If living near the danger zone, secure heavy machinery and livestock."
            ]}
          />

          <PhaseCard 
            title="During an eruption"
            icon="activity"
            iconColor="#DC2626"
            iconBg="#FEE2E2"
            tips={[
              "Stay indoors and close all windows and doors to keep volcanic ash out.",
              "Wear an N95 mask (or damp cloth) and protective goggles if you must go outside.",
              "Follow evacuation orders immediately if issued by local authorities.",
              "Avoid low-lying areas where volcanic mudflows (lahars) or poisonous gases accumulate.",
              "Do not drive in heavy ashfall as it dramatically reduces visibility and stalls engines."
            ]}
          />

          <PhaseCard 
            title="After an eruption"
            icon="shield"
            iconColor="#059669"
            iconBg="#D1FAE5"
            tips={[
              "Wait for the official 'all-clear' announcement before returning to your home.",
              "Carefully clear heavy ash from your roof to prevent structural collapse.",
              "Check on neighbors, especially the elderly or those with disabilities.",
              "Boil tap water or use bottled water until local water sources are declared safe.",
              "Keep phones clear for emergency calls; use text messaging for regular updates."
            ]}
          />
          
        </ScrollView>
      </Animated.View>
    </SafeAreaView>
  );
}

// Reusable Card Component for the Guide
const PhaseCard = ({ title, icon, iconColor, iconBg, tips }: any) => (
  <View style={styles.card}>
    <View style={styles.cardHeader}>
      <View style={[styles.iconBox, { backgroundColor: iconBg }]}>
        <Feather name={icon} size={22} color={iconColor} />
      </View>
      <Text style={styles.cardTitle}>{title}</Text>
    </View>
    
    <View>
      {tips.map((tip: string, index: number) => (
        <View key={index} style={styles.tipRow}>
          <View style={styles.bulletPoint} />
          <Text style={styles.tipText}>{tip}</Text>
        </View>
      ))}
    </View>
  </View>
);