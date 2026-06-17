import 'react-native-url-polyfill/auto';

import { Feather } from '@expo/vector-icons';
import * as ImagePicker from 'expo-image-picker';
import { useRouter } from 'expo-router';
import React, { useEffect, useRef, useState } from 'react';
import {
    ActivityIndicator,
    Alert,
    Animated,
    Image,
    SafeAreaView,
    ScrollView,
    Text,
    TextInput,
    TouchableOpacity,
    View
} from 'react-native';

import BottomNavbar from '../components/BottomNavbar';
import { ReportStyles as styles } from '../constants/theme';
import { supabase } from '../src/services/supabase';

const REPORT_TYPES = ['Ashfall', 'Tremor', 'Smoke', 'Injury', 'Other'];

export default function ReportScreen() {
    const router = useRouter();
    
    const [selectedType, setSelectedType] = useState('Ashfall');
    const [location, setLocation] = useState('');
    const [description, setDescription] = useState('');
    const [loading, setLoading] = useState(false);
    
    const [image, setImage] = useState<{ uri: string; ext: string } | null>(null);

    const fadeAnim = useRef(new Animated.Value(0)).current;
    const slideUpAnim = useRef(new Animated.Value(30)).current;

    useEffect(() => {
        Animated.parallel([
            Animated.timing(fadeAnim, { toValue: 1, duration: 600, useNativeDriver: true }),
            Animated.spring(slideUpAnim, { toValue: 0, friction: 6, useNativeDriver: true }),
        ]).start();
    }, []);

    const pickImage = async () => {
        const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
        if (status !== 'granted') {
            Alert.alert('Permission Denied', 'We need permissions to open your gallery.');
            return;
        }

        let result = await ImagePicker.launchImageLibraryAsync({
            mediaTypes: ImagePicker.MediaTypeOptions.Images,
            allowsEditing: true,
            aspect: [4, 3],
            quality: 0.2, 
        });

        if (!result.canceled) {
            const uri = result.assets[0].uri;
            const ext = uri.split('.').pop()?.toLowerCase() || 'jpeg'; 
            
            setImage({
                uri: uri,
                ext: ext === 'jpg' ? 'jpeg' : ext, 
            });
        }
    };

    const handleSubmit = async () => {
        if (!location.trim() || !description.trim()) {
            Alert.alert("Missing Information", "Please fill in both the location and description.");
            return;
        }

        setLoading(true);

        try {
            let photoUrl = null;

            if (image) {
                const fileName = `${Date.now()}.${image.ext}`; 
                const response = await fetch(image.uri);
                const blob = await response.blob();
                const { error: uploadError } = await supabase
                    .storage
                    .from('report-images')
                    .upload(fileName, blob, {
                        contentType: `image/${image.ext}`,
                        upsert: true,
                    });

                if (uploadError) {
                    Alert.alert("Storage Error!", uploadError.message);
                    setLoading(false);
                    return; 
                }
                
                const { data: publicUrlData } = supabase
                    .storage
                    .from('report-images')
                    .getPublicUrl(fileName);
                
                photoUrl = publicUrlData.publicUrl; 
            }
            
            const { error: dbError } = await supabase
                .from('reports')
                .insert([
                    { 
                        type: selectedType, 
                        location: location.trim(), 
                        description: description.trim(),
                        photo_url: photoUrl
                    }
                ]);

            if (dbError) {
                Alert.alert("Database Error!", dbError.message);
                setLoading(false);
                return;
            }

            Alert.alert("SUCCESS!", "Your report and photo have been fully submitted.");
            setLocation('');
            setDescription('');
            setImage(null);
            router.replace('/home'); 

        } catch (error: any) {
            Alert.alert("System Error", error.message || "An unexpected error occurred.");
        } finally {
            setLoading(false);
        }
    };

    return (
        <SafeAreaView style={styles.container}>
            <View style={styles.header}>
                <TouchableOpacity style={styles.backBtn} onPress={() => router.replace('/home')}>
                    <Feather name="arrow-left" size={22} color="#1E293B" />
                </TouchableOpacity>
                <Text style={styles.headerTitle}>Submit a Report</Text>
            </View>

            <Animated.View style={{ flex: 1, opacity: fadeAnim, transform: [{ translateY: slideUpAnim }] }}>
                <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>

                    <Text style={styles.label}>TYPE</Text>
                    <View style={styles.typeGrid}>
                        {REPORT_TYPES.map((type) => (
                            <TouchableOpacity
                                key={type}
                                style={[styles.typeChip, selectedType === type && styles.typeChipActive]}
                                onPress={() => setSelectedType(type)}
                            >
                                <Text style={[styles.typeChipText, selectedType === type && styles.typeChipTextActive]}>
                                    {type}
                                </Text>
                            </TouchableOpacity>
                        ))}
                    </View>

                    <Text style={styles.label}>LOCATION</Text>
                    <View style={styles.inputContainer}>
                        <Feather name="map-pin" size={18} color="#25A5FE" style={styles.inputIcon} />
                        <TextInput
                            style={styles.input}
                            placeholder="Brgy. San Nicolas, Batangas"
                            placeholderTextColor="#1E293B"
                            value={location}
                            onChangeText={setLocation}
                        />
                    </View>

                    <Text style={styles.label}>DESCRIPTION</Text>
                    <View style={styles.textAreaContainer}>
                        <TextInput
                            style={styles.textArea}
                            placeholder="Describe what you observed..."
                            placeholderTextColor="#94A3B8"
                            multiline
                            numberOfLines={4}
                            textAlignVertical="top"
                            value={description}
                            onChangeText={setDescription}
                        />
                    </View>

                    <Text style={styles.label}>PHOTO</Text>
                    <TouchableOpacity 
                        style={[styles.photoBox, image && { padding: 0, overflow: 'hidden' }]} 
                        onPress={pickImage} 
                    >
                        {image ? (
                            <>
                                <Image source={{ uri: image.uri }} style={{ width: '100%', height: 150 }} />
                                <View style={{ position: 'absolute', backgroundColor: 'rgba(0,0,0,0.5)', padding: 8, borderRadius: 20 }}>
                                    <Feather name="edit-2" size={20} color="#fff" />
                                </View>
                            </>
                        ) : (
                            <>
                                <Feather name="camera" size={32} color="#25A5FE" />
                                <Text style={styles.photoText}>Take photo or upload</Text>
                            </>
                        )}
                    </TouchableOpacity>

                    <View style={{ height: 120 }} />
                </ScrollView>
            </Animated.View>

            <View style={styles.footer}>
                <TouchableOpacity style={styles.submitBtn} onPress={handleSubmit} disabled={loading}>
                    {loading ? (
                        <ActivityIndicator color="#fff" />
                    ) : (
                        <Text style={styles.submitBtnText}>Submit Report</Text>
                    )}
                </TouchableOpacity>
            </View>

            <BottomNavbar activeTab="plus" />
        </SafeAreaView>
    );
}