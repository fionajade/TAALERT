import { Feather } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React, { useEffect, useRef, useState } from 'react';
import {
    Animated,
    SafeAreaView,
    ScrollView,
    Text,
    TextInput,
    TouchableOpacity,
    View
} from 'react-native';
import BottomNavbar from '../components/BottomNavbar';
import { ReportStyles as styles } from '../constants/theme';

const REPORT_TYPES = ['Ashfall', 'Tremor', 'Smoke', 'Injury', 'Other'];

export default function ReportScreen() {
    const router = useRouter();
    const [selectedType, setSelectedType] = useState('Ashfall');
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
                <TouchableOpacity
                    style={styles.backBtn}
                    onPress={() => router.replace('/')} 
                >
                    <Feather name="arrow-left" size={22} color="#1E293B" />
                </TouchableOpacity>
                <Text style={styles.headerTitle}>Submit a Report</Text>
            </View>

            <Animated.View style={{ flex: 1, opacity: fadeAnim, transform: [{ translateY: slideUpAnim }] }}>
                <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>

                    {/* Type Selection */}
                    <Text style={styles.label}>TYPE</Text>
                    <View style={styles.typeGrid}>
                        {REPORT_TYPES.map((type) => (
                            <TouchableOpacity
                                key={type}
                                style={[
                                    styles.typeChip,
                                    selectedType === type && styles.typeChipActive,
                                ]}
                                onPress={() => setSelectedType(type)}
                            >
                                <Text style={[
                                    styles.typeChipText,
                                    selectedType === type && styles.typeChipTextActive
                                ]}>{type}</Text>
                            </TouchableOpacity>
                        ))}
                    </View>

                    {/* Location Input */}
                    <Text style={styles.label}>LOCATION</Text>
                    <View style={styles.inputContainer}>
                        <Feather name="map-pin" size={18} color="#25A5FE" style={styles.inputIcon} />
                        <TextInput
                            style={styles.input}
                            placeholder="Brgy. San Nicolas, Batangas"
                            placeholderTextColor="#1E293B"
                        />
                    </View>

                    {/* Description Input */}
                    <Text style={styles.label}>DESCRIPTION</Text>
                    <View style={styles.textAreaContainer}>
                        <TextInput
                            style={styles.textArea}
                            placeholder="Describe what you observed..."
                            placeholderTextColor="#94A3B8"
                            multiline
                            numberOfLines={4}
                            textAlignVertical="top"
                        />
                    </View>

                    {/* Photo Upload */}
                    <Text style={styles.label}>PHOTO</Text>
                    <TouchableOpacity style={styles.photoBox}>
                        <Feather name="camera" size={32} color="#25A5FE" />
                        <Text style={styles.photoText}>Take photo or upload</Text>
                    </TouchableOpacity>

                    <View style={{ height: 120 }} />
                </ScrollView>
            </Animated.View>

            {/* Fixed Submit Button */}
            <View style={styles.footer}>
                <TouchableOpacity style={styles.submitBtn}>
                    <Text style={styles.submitBtnText}>Submit Report</Text>
                </TouchableOpacity>
            </View>

            <BottomNavbar activeTab="plus" />
        </SafeAreaView>
    );
}