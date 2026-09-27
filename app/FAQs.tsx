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
                <Text style={styles.headerTitle}>Frequently Asked Questions</Text>
            </View>

            <Animated.View style={{ flex: 1, opacity: fadeAnim, transform: [{ translateY: slideUpAnim }] }}>
                <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>

                    <PhaseCard
                        title="How do I submit an incident report?"
                        icon="file-text"
                        iconColor="#D97706"
                        iconBg="#FEF3C7"
                        tips={[
                            "Select the type of incident, provide the required information, add the location, and submit the report."
                        ]}
                    />

                    <PhaseCard
                        title="How can I check my report status?"
                        icon="clipboard"
                        iconColor="#2563EB"
                        iconBg="#DBEAFE"
                        tips={[
                            "Go to “My Reports” to see the current status and latest updates."
                        ]}
                    />

                    <PhaseCard
                        title="How do I use the SOS feature?"
                        icon="alert-triangle"
                        iconColor="#DC2626"
                        iconBg="#FEE2E2"
                        tips={[
                            "Tap the SOS button and press and hold it for five seconds to send an emergency request."
                        ]}
                    />

                    <PhaseCard
                        title="How can I find an evacuation center?"
                        icon="map-pin"
                        iconColor="#059669"
                        iconBg="#D1FAE5"
                        tips={[
                            "Open the Evacuation section to view evacuation information relevant to your current location."
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