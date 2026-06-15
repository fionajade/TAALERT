import { Feather } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React, { useEffect, useRef, useState } from 'react';
import {
    Animated,
    KeyboardAvoidingView,
    Platform,
    SafeAreaView,
    StatusBar,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View
} from 'react-native';

const COLORS = {
  background: '#F5F7FA',
  primaryBlue: '#319EFE', 
  darkNavy: '#223354',    
  textDark: '#1A202C',
  textMuted: '#718096',
  white: '#FFFFFF',
  inputBg: '#FFFFFF',
  inputBorder: '#E2E8F0',
};

export default function SignUpScreen() {
  const router = useRouter();

  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);

  // Entrance Animations
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const slideUpAnim = useRef(new Animated.Value(30)).current;

  useEffect(() => {
    Animated.parallel([
      Animated.timing(fadeAnim, { toValue: 1, duration: 600, useNativeDriver: true }),
      Animated.spring(slideUpAnim, { toValue: 0, friction: 6, useNativeDriver: true }),
    ]).start();
  }, []);

  const handleSignUp = () => {
    // After signing up, replace the screen with the home screen
    router.replace('/home'); 
  };

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="dark-content" backgroundColor={COLORS.background} />

      <KeyboardAvoidingView 
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={{ flex: 1 }}
      >
        <Animated.View 
          style={[
            styles.innerContainer, 
            { opacity: fadeAnim, transform: [{ translateY: slideUpAnim }] }
          ]}
        >
          {/* --- Top Bar / Back Button --- */}
          <View style={styles.topBar}>
            <TouchableOpacity 
              style={styles.backBtn} 
              onPress={() => router.back()} // Goes back to the previous screen (Login)
            >
              <Feather name="arrow-left" size={24} color={COLORS.textDark} />
            </TouchableOpacity>
          </View>

          {/* --- Header Texts --- */}
          <View style={styles.headerContainer}>
            <Text style={styles.titleText}>Create Account ✨</Text>
            <Text style={styles.subText}>Sign up to get real-time disaster alerts and stay protected.</Text>
          </View>

          {/* --- Form Inputs --- */}
          <View style={styles.formContainer}>
            
            {/* Full Name Input */}
            <View style={styles.inputWrapper}>
              <Feather name="user" size={20} color={COLORS.textMuted} style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                placeholder="Full Name"
                placeholderTextColor={COLORS.textMuted}
                autoCapitalize="words"
                value={name}
                onChangeText={setName}
              />
            </View>

            {/* Email Input */}
            <View style={styles.inputWrapper}>
              <Feather name="mail" size={20} color={COLORS.textMuted} style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                placeholder="Email address"
                placeholderTextColor={COLORS.textMuted}
                keyboardType="email-address"
                autoCapitalize="none"
                value={email}
                onChangeText={setEmail}
              />
            </View>

            {/* Password Input */}
            <View style={styles.inputWrapper}>
              <Feather name="lock" size={20} color={COLORS.textMuted} style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                placeholder="Create Password"
                placeholderTextColor={COLORS.textMuted}
                secureTextEntry={!showPassword}
                value={password}
                onChangeText={setPassword}
              />
              <TouchableOpacity 
                style={styles.eyeIcon} 
                onPress={() => setShowPassword(!showPassword)}
              >
                <Feather 
                  name={showPassword ? "eye" : "eye-off"} 
                  size={20} 
                  color={COLORS.textMuted} 
                />
              </TouchableOpacity>
            </View>

            {/* Sign Up Button */}
            <TouchableOpacity 
              style={styles.signUpBtn} 
              onPress={handleSignUp}
              activeOpacity={0.8}
            >
              <Text style={styles.signUpBtnText}>Sign Up</Text>
            </TouchableOpacity>
          </View>

          {/* --- Footer Log In --- */}
          <View style={styles.footerContainer}>
            <Text style={styles.footerText}>Already have an account? </Text>
            {/* Navigates back to the Login index */}
            <TouchableOpacity onPress={() => router.back()}>
              <Text style={styles.logInText}>Log In</Text>
            </TouchableOpacity>
          </View>

        </Animated.View>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  innerContainer: {
    flex: 1,
    paddingHorizontal: 24,
    justifyContent: 'center',
  },
  topBar: {
    position: 'absolute',
    top: Platform.OS === 'android' ? 20 : 0,
    left: 24,
    zIndex: 10,
  },
  backBtn: {
    padding: 8,
    marginLeft: -8, // Offset the padding so the icon aligns perfectly with the text
  },
  headerContainer: {
    marginBottom: 40,
    marginTop: 40, // Added margin to clear the back button
  },
  titleText: {
    fontSize: 28,
    fontWeight: '800',
    color: COLORS.textDark,
    marginBottom: 8,
  },
  subText: {
    fontSize: 15,
    color: COLORS.textMuted,
    lineHeight: 22,
  },
  formContainer: {
    marginBottom: 24,
  },
  inputWrapper: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.inputBg,
    borderRadius: 16, 
    marginBottom: 16,
    paddingHorizontal: 16,
    height: 60,
  },
  inputIcon: {
    marginRight: 12,
  },
  input: {
    flex: 1,
    fontSize: 16,
    color: COLORS.textDark,
    height: '100%',
  },
  eyeIcon: {
    padding: 8,
  },
  signUpBtn: {
    backgroundColor: COLORS.primaryBlue,
    borderRadius: 16,
    height: 60,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 16, // Added extra space above the button
    shadowColor: COLORS.primaryBlue,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 6,
  },
  signUpBtnText: {
    color: COLORS.white,
    fontSize: 16,
    fontWeight: 'bold',
  },
  footerContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 20,
  },
  footerText: {
    color: COLORS.textMuted,
    fontSize: 14,
  },
  logInText: {
    color: COLORS.primaryBlue,
    fontSize: 14,
    fontWeight: 'bold',
  },
});